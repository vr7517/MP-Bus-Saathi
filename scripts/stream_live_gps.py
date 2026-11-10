#!/usr/bin/env python3
"""
MP Bus Saathi - Continuous Live GPS Streamer
Emits realistic live GPS pings for Bus 1 (MP-04-EA-1024) every 6 seconds
along the Bhopal -> Sehore -> Ashta -> Dewas -> Indore corridor.
"""

import time
import requests
import datetime
import math

API_URL = "http://127.0.0.1:8000/api/v1/telemetry/ping"
BUS_ID = 1
SCHEDULE_ID = 1

WAYPOINTS = [
    {"name": "नादरा बस स्टैंड (Nadra Bus Stand)", "lat": 23.2642, "lng": 77.4124, "speed": 22.0, "heading": 240.0},
    {"name": "लालघाटी चौराहा (Lalghati)", "lat": 23.2790, "lng": 77.3710, "speed": 40.0, "heading": 250.0},
    {"name": "बैरागढ़ बस स्टैंड (Bairagarh)", "lat": 23.2842, "lng": 77.3350, "speed": 45.0, "heading": 255.0},
    {"name": "खजूरी सड़क (Khajuri Sadak)", "lat": 23.2680, "lng": 77.2210, "speed": 68.0, "heading": 250.0},
    {"name": "सीहोर क्रिसेंट चौराहा (Sehore Crescent)", "lat": 23.2080, "lng": 77.0850, "speed": 65.0, "heading": 245.0},
    {"name": "सीहोर मुख्य बस स्टैंड (Sehore Main)", "lat": 23.2005, "lng": 77.0833, "speed": 15.0, "heading": 235.0},
    {"name": "आष्टा बाईपास (Ashta Bypass)", "lat": 23.0180, "lng": 76.7190, "speed": 72.0, "heading": 230.0},
    {"name": "कन्नौद फाटा (Kannod Phata)", "lat": 22.9910, "lng": 76.4520, "speed": 75.0, "heading": 235.0},
    {"name": "देवास बस स्टैंड (Dewas Bus Stand)", "lat": 22.9676, "lng": 76.0534, "speed": 20.0, "heading": 220.0},
    {"name": "मांगलिया (Manglia)", "lat": 22.8120, "lng": 75.9210, "speed": 62.0, "heading": 215.0},
    {"name": "सरवटे बस स्टैंड, इंदौर (Sarwate Indore)", "lat": 22.7126, "lng": 75.8672, "speed": 10.0, "heading": 210.0},
]

def run_loop():
    print(f"[*] MP Bus Saathi continuous GPS simulation started for Bus {BUS_ID}...")
    idx = 0
    while True:
        wp = WAYPOINTS[idx % len(WAYPOINTS)]
        payload = {
            "bus_id": BUS_ID,
            "latitude": wp["lat"],
            "longitude": wp["lng"],
            "speed_kmh": wp["speed"],
            "heading": wp["heading"],
            "timestamp": datetime.datetime.now(datetime.timezone.utc).isoformat(),
            "active_schedule_id": SCHEDULE_ID
        }
        try:
            res = requests.post(API_URL, json=payload, timeout=4)
            print(f"[{time.strftime('%H:%M:%S')}] Bus {BUS_ID} @ {wp['name']} ({wp['speed']} km/h) -> HTTP {res.status_code}")
        except Exception as e:
            print(f"[{time.strftime('%H:%M:%S')}] Ping error: {e}")

        idx += 1
        time.sleep(6)

if __name__ == "__main__":
    run_loop()
