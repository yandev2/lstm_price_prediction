import os
import sys
import json
import argparse
import pickle
import numpy as np
import pandas as pd
from datetime import datetime

# =========================================================
# MEMATIKAN LOG TENSORFLOW AGAR TIDAK MERUSAK OUTPUT JSON
# =========================================================
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
import tensorflow as tf
tf.get_logger().setLevel('ERROR')
from tensorflow.keras.models import load_model

# =========================================================
# CONFIGURASI
# =========================================================
TIMESTEPS = 30
MIN_HISTORY_REQUIRED = 60

FEATURE_COLUMNS = [
    "harga", "volume_distribusi", "pasar_encoded", "ramadhan", "idul_fitri",
    "natal_tahun_baru", "hujan", "operasi_pasar", "gangguan_distribusi",
    "banjir", "kearifan_score", "harga_lag_1", "harga_lag_2", "harga_lag_3",
    "harga_lag_7", "harga_lag_14", "harga_lag_30", "ma_3", "ma_7", "ma_14",
    "tahun", "bulan", "hari", "hari_dalam_minggu"
]

# =========================================================
# FUNGSI UTAMA
# =========================================================
def build_feature_sequence(payload, encoder):
    historical_data = payload.get("historical_data", [])
    if len(historical_data) < MIN_HISTORY_REQUIRED:
        raise ValueError(f"Minimal {MIN_HISTORY_REQUIRED} historical_data dibutuhkan")

    pasar = payload.get("pasar", "")
    kearifan_score = float(payload.get("kearifan_score", 0))
    scenario_events = payload.get("event", {})

    historical_data = sorted(historical_data, key=lambda x: x['tanggal'])
    total_history = len(historical_data)

    if total_history < (TIMESTEPS + 30):
        raise ValueError(f"Minimal {TIMESTEPS + 30} historical_data dibutuhkan")

    if pasar not in encoder.classes_:
        raise ValueError(f"Pasar '{pasar}' tidak dikenal oleh encoder")
    pasar_encoded = int(encoder.transform([pasar])[0])

    rows = []
    start_index = total_history - TIMESTEPS

    for i in range(start_index, total_history):
        current_row = historical_data[i]
        is_last_step = (i == total_history - 1)
        event_source = scenario_events if (is_last_step and scenario_events) else current_row

        current_price = float(current_row['harga'])
        current_volume = float(current_row.get('volume_distribusi', 0))
        current_date = datetime.strptime(current_row['tanggal'], '%Y-%m-%d %H:%M:%S')

        prices_before = [float(x['harga']) for x in historical_data[:i]]

        row = {
            "harga": current_price,
            "volume_distribusi": current_volume,
            "pasar_encoded": pasar_encoded,
            "ramadhan": float(event_source.get("ramadhan", 0)),
            "idul_fitri": float(event_source.get("idul_fitri", 0)),
            "natal_tahun_baru": float(event_source.get("natal_tahun_baru", 0)),
            "hujan": float(event_source.get("hujan", 0)),
            "operasi_pasar": float(event_source.get("operasi_pasar", 0)),
            "gangguan_distribusi": float(event_source.get("gangguan_distribusi", 0)),
            "banjir": float(event_source.get("banjir", 0)),
            "kearifan_score": kearifan_score,
            "harga_lag_1": prices_before[-1],
            "harga_lag_2": prices_before[-2],
            "harga_lag_3": prices_before[-3],
            "harga_lag_7": prices_before[-7],
            "harga_lag_14": prices_before[-14],
            "harga_lag_30": prices_before[-30],
            "ma_3": np.mean(prices_before[-3:]),
            "ma_7": np.mean(prices_before[-7:]),
            "ma_14": np.mean(prices_before[-14:]),
            "tahun": current_date.year,
            "bulan": current_date.month,
            "hari": current_date.day,
            "hari_dalam_minggu": current_date.weekday()
        }
        rows.append(row)

    df = pd.DataFrame(rows)[FEATURE_COLUMNS]
    if len(df) != TIMESTEPS:
        raise ValueError(f"Jumlah timestep tidak sesuai. Expected={TIMESTEPS}, got={len(df)}")
    
    return df

def preprocess_and_scale(df, scaler):
    matrix_2d = df.values.astype(np.float64)
    scaled = scaler.transform(matrix_2d)
    if np.isnan(scaled).any():
        raise ValueError("Terdapat NaN setelah scaling")
    return scaled.reshape((1, TIMESTEPS, len(FEATURE_COLUMNS)))

def inverse_scale_y(pred_scaled, scaler):
    dummy = np.zeros((1, len(FEATURE_COLUMNS)), dtype=np.float64)
    dummy[0, 0] = pred_scaled
    return float(scaler.inverse_transform(dummy)[0, 0])

# =========================================================
# EKSEKUSI UTAMA (CLI)
# =========================================================
if __name__ == '__main__':
    # Tangkap argumen dari command line (dikirim oleh Laravel)
    parser = argparse.ArgumentParser()
    parser.add_argument('--payload', required=True, help='Path ke file JSON payload')
    parser.add_argument('--model', required=True, help='Path ke file model .h5')
    parser.add_argument('--scaler', required=True, help='Path ke file scaler .pkl')
    parser.add_argument('--encoder', required=True, help='Path ke file encoder pasar .pkl')
    parser.add_argument('--metrics', required=False, help='Path ke file metrics .json (opsional)')
    args = parser.parse_args()

    try:
        # 1. Load Payload
        with open(args.payload, 'r') as f:
            payload = json.load(f)

        # 2. Load Artifacts
        model = load_model(args.model)
        
        with open(args.scaler, 'rb') as f:
            scaler = pickle.load(f)
            
        with open(args.encoder, 'rb') as f:
            encoder = pickle.load(f)

        historical_mape = 4.32
        if args.metrics and os.path.exists(args.metrics):
            with open(args.metrics, 'r') as f:
                metrics_data = json.load(f)
                historical_mape = metrics_data.get('mape', 4.32)

        # 3. Proses Data
        df = build_feature_sequence(payload, encoder)
        current_price = float(df.iloc[-1]['harga'])
        features_3d = preprocess_and_scale(df, scaler)

        # 4. Prediksi
        prediction = model.predict(features_3d, verbose=0)
        scaled_prediction = float(prediction[0][0])
        predicted_price = inverse_scale_y(scaled_prediction, scaler)

        # 5. Kalkulasi Anomali
        raw_diff_percent = ((predicted_price - current_price) / current_price) * 100
        
        status_anomali = "normal"
        if raw_diff_percent >= 15:
            status_anomali = "lonjakan"
        elif raw_diff_percent <= -15:
            status_anomali = "penurunan ekstrem"

        tanggal_sekarang = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

        # 6. Susun Respons
        response = {
            'success': True,
            'data': {
                'tanggal_prediksi': tanggal_sekarang,
                'harga_saat_ini': round(current_price, 2),
                'harga_prediksi': round(predicted_price, 2),
                'selisih_persen': round(raw_diff_percent, 2),
                'status_anomali': status_anomali,
                'alert_harga': abs(raw_diff_percent) >= 15,
                'historical_mape': historical_mape
            }
        }
        # CETAK JSON (Ini akan ditangkap oleh Laravel)
        print(json.dumps(response))

    except Exception as e:
        # Jika terjadi error, cetak error dalam format JSON agar bisa dibaca Laravel
        error_response = {
            'success': False,
            'message': str(e)
        }
        print(json.dumps(error_response))
        sys.exit(1)