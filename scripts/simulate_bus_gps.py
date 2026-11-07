#!/usr/bin/env python3
"""
MP Bus Saathi - GPS Telemetry Simulator
Simulates a bus transmitting periodic GPS pings between Bhopal and Indore.
"""

import time
import requests
import json
import sys

API_URL = "http://localhost:8000/api/v1/telemetry/ping"
BUS_ID = 1
SCHEDULE_ID = 1

# Sample coordinates along Bhopal -> Sehore -> Ashta -> Dewas -> Indore
WAYPOINTS = [
    {"name": "Bhopal (Nadra Bus Stand)", "lat": 23.2642, "lng": 77.4124, "speed": 25.0},
    {"name": "Bairagarh", "lat": 23.2842, "lng": 77.3350, "speed": 45.0},
    {"name": "Sehore Crescent", "lat": 23.2080, "lng": 77.0850, "speed": 65.0},
    {"name": "Sehore Bus Stand", "lat": 23.2005, "lng": 77.0833, "speed": 10.0},
    {"name": "Ashta Bypass", "lat": 23.0180, "lng": 76.7190, "speed": 70.0},
    {"name": "Dewas Bypass", "lat": 22.9676, "lng": 76.0534, "speed": 60.0},
    {"name": "Indore Sarwate Bus Stand", "lat": 22.7126, "lng": 75.8672, "speed": 15.0},
]

def simulate():
    print(f"Starting GPS simulation for Bus ID {BUS_ID}...")
    for idx, point in enumerate(WAYPOINTS):
        payload = {
            "bus_id": BUS_ID,
            "latitude": point["lat"],
            "longitude": point["lng"],
            "speed_kmh": point["speed"],
            "heading": 240.0,
            "timestamp": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
            "active_schedule_id": SCHEDULE_ID
        }
        try:
            res = requests.post(API_URL, json=payload, timeout=5)
            print(f"[{idx+1}/{len(WAYPOINTS)}] Emitted: {point['name']} -> Status: {res.status_code}")
        except Exception as e:
            print(f"[{idx+1}/{len(WAYPOINTS)}] Ping to {point['name']} failed (Backend offline?): {e}")
        
        time.sleep(2)

if __name__ == "__main__":
    simulate()
