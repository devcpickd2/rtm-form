@extends('layouts.blank')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap');

  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --neon-red:    #FF1744;
    --neon-blue:   #00B8D4;
    --neon-green:  #00E676;
    --neon-purple: #AB47BC;
    --neon-yellow: #FFEA00;
    --glass-bg:    rgba(255, 255, 255, 0.1);
    --glass-blur:  rgba(255, 255, 255, 0.15);
    --glass-border: rgba(255, 255, 255, 0.3);
    --dark-bg:     #0A0A0F;
    --dark-panel:  rgba(15, 15, 25, 0.8);
  }

  html, body {
    font-family: 'Inter', sans-serif;
    background: var(--dark-bg);
    color: #FFFFFF;
    height: 100vh;
    overflow: hidden;
  }

  body::before {
    content: '';
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: 
    radial-gradient(circle at 20% 80%, rgba(120,119,198,0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255,119,198,0.3) 0%, transparent 50%);
    animation: bgShift 20s ease-in-out infinite;
    z-index: -2;
  }

  @keyframes bgShift {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }

  /* INTERAKTIF: Enhanced Particles */
  .particles {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    pointer-events: none;
    z-index: -1;
  }

  .particle {
    position: absolute;
    background: var(--neon-blue);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
    box-shadow: 0 0 15px currentColor;
    transition: all 0.3s ease;
  }

  .particle.hover {
    transform: scale(2) !important;
    box-shadow: 0 0 30px currentColor !important;
    filter: hue-rotate(180deg);
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.5; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
  }

  .screen {
    position: fixed;
    inset: 0;
    opacity: 0;
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    pointer-events: none;
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
  }
  .screen.active { 
    opacity: 1; 
    pointer-events: auto;
    transform: scale(1.02);
  }
  .screen.leaving { transform: scale(0.98); }

  .topbar {
    height: 10vh;
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 4vw;
    background: rgba(10,10,15,0.95);
    backdrop-filter: blur(25px);
    border-bottom: 1px solid rgba(255,255,255,0.15);
    flex-shrink: 0;
    position: relative;
  }

  .topbar::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, var(--neon-blue), var(--neon-purple), var(--neon-red));
    animation: glowLine 3s ease-in-out infinite alternate;
  }

  @keyframes glowLine {
    0% { box-shadow: 0 0 5px var(--neon-blue); }
    100% { box-shadow: 0 0 15px var(--neon-red); }
  }

  .brand {
    font-family: 'Orbitron', monospace;
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 900;
    background: linear-gradient(135deg, var(--neon-blue), var(--neon-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: brandGlow 2s ease-in-out infinite alternate;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .brand:hover {
    transform: scale(1.05);
    filter: drop-shadow(0 0 20px var(--neon-purple));
  }

  @keyframes brandGlow {
    0% { filter: drop-shadow(0 0 5px var(--neon-blue)); }
    100% { filter: drop-shadow(0 0 15px var(--neon-purple)); }
  }

  .screen-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-family: 'Orbitron', monospace;
    font-size: clamp(20px, 1.5vw, 15px);
    font-weight: 700;
    letter-spacing: 3px;
    background: linear-gradient(135deg, var(--neon-green), var(--neon-yellow));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: titleGlitch 3s infinite;
  }

  @keyframes titleGlitch {
    0%, 90%, 100% { transform: translateX(-50%); }
    92% { transform: translateX(-52%); }
    94% { transform: translateX(-48%); }
    96% { transform: translateX(-51%); }
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 2vw;
  }

  .live-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(0,255,136,0.25);
    color: var(--neon-green);
    font-size: clamp(11px, 1.2vw, 13px);
    font-weight: 700;
    letter-spacing: 1.5px;
    padding: 8px 16px;
    border-radius: 20px;
    border: 1px solid rgba(0,255,136,0.5);
    box-shadow: 0 0 10px rgba(0,255,136,0.3);
    animation: pulseLive 2s ease-in-out infinite;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .live-pill:hover {
    transform: scale(1.1) rotate(2deg);
    box-shadow: 0 0 20px rgba(0,255,136,0.6);
  }

  @keyframes pulseLive {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }

  .live-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--neon-green);
    animation: pulse-glow 1.5s infinite;
    box-shadow: 0 0 10px var(--neon-green);
  }

  @keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 10px var(--neon-green); }
    50% { box-shadow: 0 0 20px var(--neon-green); }
  }

  .clock {
    font-family: 'Orbitron', monospace;
    font-size: clamp(22px, 3vw, 28px);
    font-weight: 900;
    background: linear-gradient(135deg, var(--neon-blue), var(--neon-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .clock-date {
    font-size: clamp(10px, 1.1vw, 12px);
    color: rgba(255,255,255,0.8);
    margin-top: 2px;
  }

  .body-content {
    flex: 1;
    padding: 4vw;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 3vh;
  }

  .glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(25px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  }

  .glass-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.5);
  }

  .glass-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--neon-blue), var(--neon-purple), var(--neon-red));
  }

  /* LAYOUT BARU: PROG CARD LEBIH KECIL, TBL LEBIH TINGGI */
  .main-grid {
    display: grid;
    grid-template-columns: 0.35fr 1.65fr;
    grid-template-rows: 0.38fr 0.62fr;
    gap: 3vh 3vw;
    height: 100%;
  }

  .big-stat {
    grid-column: 1;
    grid-row: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 3vh 2vw;
    background: linear-gradient(145deg, rgba(255,0,64,0.2), rgba(255,215,0,0.15));
    border: 1px solid rgba(255,0,64,0.4);
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .big-stat:hover {
    transform: scale(1.02);
    box-shadow: 0 0 30px rgba(255,0,64,0.5);
  }

  .big-stat::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 30%, rgba(255,0,64,0.25) 0%, transparent 50%);
  }

  .bs-label {
    font-size: clamp(12px, 1.4vw, 14px);
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.95);
    margin-bottom: 1vh;
    position: relative;
    z-index: 1;
  }

  .bs-num {
    font-family: 'Orbitron', monospace;
    font-size: clamp(60px, 14vw, 90px);
    font-weight: 900;
    background: linear-gradient(135deg, #FFFFFF, var(--neon-yellow));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    position: relative;
    z-index: 1;
    animation: countPulse 2s ease-in-out infinite;
  }

  @keyframes countPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }

  .bs-sub {
    font-size: clamp(12px, 1.4vw, 14px);
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.9);
    margin-top: 1vh;
    position: relative;
    z-index: 1;
  }

  /* PROGRESS CARD - FONT BESAR & INFO LENGKAP */
  .prog-card {
    grid-column: 2;
    grid-row: 1;
    padding: 3vh 3vw;
    display: flex;
    flex-direction: column;
    gap: 2vh;
    min-height: 0;
  }

  .no-active-state {
    display: flex !important;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 70% !important;
    color: rgba(255,255,255,0.6);
    gap: 1vh;
    text-align: center;
    animation: fadeInUp 1s ease-out;
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .no-active-state .no-active-title {
    font-family: 'Orbitron', monospace;
    font-size: clamp(24px, 3.5vw, 28px) !important;
    font-weight: 900;
    color: var(--neon-blue);
  }

  .no-active-state .no-active-subtitle {
    font-size: clamp(14px, 1.6vw, 16px) !important;
  }

  .prog-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2vw;
  }

  .prog-left h3 {
    font-family: 'Orbitron', monospace;
    font-size: clamp(18px, 2.2vw, 22px); /* DIPERBESAR */
    font-weight: 800;
    color: var(--neon-blue);
    margin-bottom: 0.5vh;
    line-height: 1.2;
  }

  .prog-left .product-name {
    font-size: clamp(14px, 1.6vw, 16px); /* DIPERBESAR */
    color: rgba(255,255,255,0.95);
    font-weight: 600;
    margin-bottom: 0.5vh;
    line-height: 1.3;
    max-width: 65%;
  }

  .prog-left .sub-info {
    font-size: clamp(12px, 1.3vw, 14px);
    color: rgba(255,255,255,0.8);
    font-weight: 500;
  }

  .prog-right {
    text-align: right;
  }

  .prog-pct {
    font-family: 'Orbitron', monospace;
    font-size: clamp(48px, 8vw, 65px); /* DIPERBESAR */
    font-weight: 900;
    background: linear-gradient(135deg, var(--neon-green), var(--neon-yellow));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 1vh;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: clamp(13px, 1.4vw, 15px); /* DIPERBESAR */
    font-weight: 800;
    padding: 8px 16px;
    border-radius: 25px;
    text-transform: uppercase;
    box-shadow: 0 0 15px rgba(255,255,255,0.2);
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .status-badge:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 0 25px rgba(255,255,255,0.4);
  }

  .status-badge.selesai { 
    background: rgba(0,255,136,0.3); 
    color: var(--neon-green); 
    border: 1px solid rgba(0,255,136,0.6);
  }
  .status-badge.proses { 
    background: rgba(0,212,255,0.3); 
    color: var(--neon-blue); 
    border: 1px solid rgba(0,212,255,0.6);
  }

  .time-info {
    display: flex;
    gap: 2vw;
    font-size: clamp(12px, 1.3vw, 13px);
    color: rgba(255,255,255,0.85);
    margin-top: 1vh;
    padding: 8px 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    background: rgba(255,255,255,0.03);
  }

  .time-start, .time-end {
    text-align: center;
    font-weight: 600;
    line-height: 1.3;
    min-width: 60px;
  }

  .time-running {
    text-align: center;
    font-weight: 700;
    line-height: 1.3;
    animation: pulseLive 1.5s ease-in-out infinite;
  }

  @keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.4; }
  }

/* Hover effect */
.prog-left:hover .time-info {
  background: rgba(0,212,255,0.1);
  box-shadow: 0 0 15px rgba(0,212,255,0.2);
  transform: scale(1.02);
}

.prog-left:hover .time-running {
  color: var(--neon-yellow);
  text-shadow: 0 0 10px var(--neon-yellow);
}

.pbar-track {
  height: clamp(20px, 2.5vw, 24px); /* DIPERBESAR */
  background: rgba(255,255,255,0.08);
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.2);
  overflow: hidden;
  position: relative;
}

.pbar-track::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.pbar-fill {
  height: 100%;
  border-radius: 12px;
  transition: width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  box-shadow: 0 0 15px currentColor;
  position: relative;
  overflow: hidden;
}

.pbar-fill::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  animation: progressShine 1.5s infinite;
}

@keyframes progressShine {
  0% { transform: translateX(-100%) scaleX(0.5); }
  100% { transform: translateX(100%) scaleX(0.5); }
}

.pbar-fill.selesai { background: linear-gradient(90deg, var(--neon-green), #00FF88); }
.pbar-fill.proses { background: linear-gradient(90deg, var(--neon-blue), #00D4FF); }

/* TABEL - LEBIH LENGKAP & INFO SUB PRODUKSI */
.tbl-card {
  grid-column: 2;
  grid-row: 2;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.tbl-head {
  padding: 2.5vh 3vw;
  background: rgba(255,255,255,0.03);
  border-bottom: 1px solid rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  gap: 1vw;
  flex-shrink: 0;
}

.tbl-head-icon {
  width: 4px; height: 24px;
  background: linear-gradient(180deg, var(--neon-red), var(--neon-yellow));
  border-radius: 2px;
  box-shadow: 0 0 10px var(--neon-red);
  animation: iconPulse 1.5s infinite;
}

@keyframes iconPulse {
  0%, 100% { height: 24px; }
  50% { height: 28px; }
}

.tbl-head-title {
  font-size: clamp(13px, 1.5vw, 15px); /* DIPERBESAR */
  font-weight: 800;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.95);
}

.rolling-container {
  flex: 1;
  overflow: hidden;
  position: relative;
}

.rolling-list {
  display: flex;
  flex-direction: column;
  animation: rollUp 28s linear infinite;
  padding: 2vh 0;
  will-change: transform;
}

.rolling-list:hover {
  animation-play-state: paused;
}

@keyframes rollUp {
  0% { transform: translateY(0); }
  100% { transform: translateY(-100%); }
}

.jam-table {
  width: 100%;
  border-collapse: collapse;
}

.jam-table th {
  padding: 1.5vh 2vw;
  text-align: center;
  font-size: clamp(12px, 1.3vw, 13px); /* DIPERBESAR */
  font-weight: 800;
  color: rgba(255,255,255,0.8);
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.jam-table td {
  padding: 1.8vh 2vw; /* DIPERBESAR */
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  font-size: clamp(13px, 1.5vw, 14px); /* DIPERBESAR */
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  vertical-align: middle;
}

.jam-table tr {
  background: rgba(255,255,255,0.02);
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  cursor: pointer;
  position: relative;
  height: 70px; /* TINGGI BARIS DIPERBESAR */
}

.jam-table tr::before {
  content: '';
  position: absolute;
  left: 0; top: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.jam-table tr:hover {
  background: rgba(255,255,255,0.15) !important;
  transform: scale(1.02) translateX(5px);
  box-shadow: 0 10px 30px rgba(0,212,255,0.3);
}

.jam-table tr:hover::before {
  opacity: 1;
  animation: rowShine 1.5s infinite;
}

@keyframes rowShine {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.jam-table tr.row-active {
  background: rgba(0,212,255,0.25) !important;
  box-shadow: 0 0 25px rgba(0,212,255,0.4);
  animation: rowActiveGlow 2s infinite;
}

@keyframes rowActiveGlow {
  0%, 100% { box-shadow: 0 0 25px rgba(0,212,255,0.4); }
  50% { box-shadow: 0 0 35px rgba(0,212,255,0.6); }
}

.product-name-cell {
  font-weight: 700 !important;
  max-width: 200px;
  white-space: normal !important;
  line-height: 1.3 !important;
  font-size: clamp(14px, 1.6vw, 15px) !important; /* DIPERBESAR */
}

.code-cell {
  font-family: 'Orbitron', monospace !important;
  color: var(--neon-blue) !important;
  font-weight: 800 !important;
  font-size: clamp(14px, 1.6vw, 15px) !important; /* DIPERBESAR */
}

.product-name-cell {
  font-weight: 700 !important;
  max-width: 220px !important; /* DIPERLEBAR */
  white-space: normal !important;
  line-height: 1.3 !important;
  font-size: clamp(14px, 1.6vw, 15px) !important;
  padding-right: 8px !important;
}

/* Sub produk style */
.product-name-cell > div:last-child {
  font-size: clamp(11px, 1.2vw, 12px) !important;
  opacity: 0.95;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

/* Hover effect untuk sub produk */
.jam-table tr:hover .product-name-cell > div:last-child {
  color: var(--neon-purple) !important;
  text-shadow: 0 0 8px var(--neon-purple);
  transform: scale(1.05);
}

/* SUHU GRID - DIPERBAIKI */
.suhu-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  grid-template-rows: repeat(3, 1fr);
  gap: 2vh 2vw;
  height: 100%;
}

.suhu-tile {
  padding: 2vh 1.5vw;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 1vh;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  border-radius: 16px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.suhu-tile::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
  transform: translateX(-100%) translateY(-100%) rotate(45deg);
  transition: all 0.6s ease;
  opacity: 0;
}

.suhu-tile:hover::before {
  opacity: 1;
  transform: translateX(100%) translateY(100%) rotate(45deg);
}

.suhu-tile:hover {
  transform: translateY(-12px) scale(1.02) rotateX(5deg);
  box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}

.suhu-tile:active {
  transform: translateY(-8px) scale(0.98);
}

.tile-name {
  font-size: clamp(12px, 1.8vw, 15px);
  font-weight: 800;
  letter-spacing: 1px;
  color: rgba(255,255,255,1);
  text-transform: uppercase;
  text-align: center;
  text-shadow: 0 0 10px rgba(255,255,255,0.5);
}

.tile-temp {
  font-family: 'Orbitron', monospace;
  font-size: clamp(32px, 6vw, 45px);
  font-weight: 900;
  line-height: 1;
  text-shadow: 0 0 20px currentColor;
  transition: all 0.3s ease;
}

.tile-temp:hover {
  text-shadow: 0 0 30px currentColor;
  transform: scale(1.1);
  filter: drop-shadow(0 0 10px currentColor);
}

.tile-cold .tile-temp { color: #00E5FF; }
.tile-cool .tile-temp { color: #4DD0E1; }
.tile-warm .tile-temp { color: #FFEE58; }
.tile-hot .tile-temp  { color: #FF5252; }
.tile-warning .tile-temp { 
  color: var(--neon-red) !important; 
  animation: warningPulse 1s infinite !important; 
}
.tile-warning {
  background: rgba(255, 193, 7, 0.2) !important;
  border: 2px solid #FFEA00 !important;
  box-shadow: 0 0 30px rgba(255, 193, 7, 0.6) !important;
}

@keyframes warningPulse {
  0%, 100% { 
    filter: hue-rotate(0deg) brightness(1);
    text-shadow: 0 0 20px var(--neon-red);
  }
  50% { 
    filter: hue-rotate(0deg) brightness(1.2);
    text-shadow: 0 0 35px var(--neon-red), 0 0 45px var(--neon-red);
  }
}

.tile-warning:hover {
  animation: warningShake 0.5s ease-in-out infinite alternate !important;
}

@keyframes warningShake {
  0% { transform: translateX(0) rotate(0deg); }
  100% { transform: translateX(3px) rotate(1deg); }
}

.tile-warning .tile-name {
  color: var(--neon-red) !important;
  text-shadow: 0 0 15px var(--neon-red) !important;
}
.tile-warning .tile-rh {
  background: rgba(255, 193, 7, 0.3) !important;
  border-color: #FFEA00 !important;
  color: #FFEA00 !important;
  box-shadow: 0 0 15px rgba(255, 234, 0, 0.6) !important;
}

.warning-temp {
  animation: warningPulse 1s infinite !important;
}

.warning-rh {
  background: rgba(255, 23, 68, 0.4) !important;
  border-color: var(--neon-red) !important;
  color: var(--neon-red) !important;
  box-shadow: 0 0 20px rgba(255, 23, 68, 0.8) !important;
  animation: warningPulse 1.2s infinite !important;
}

.tile-unit {
  font-size: clamp(12px, 1.4vw, 14px);
  color: rgba(255,255,255,0.95);
  font-weight: 700;
}

.tile-rh {
  font-size: clamp(11px, 1.3vw, 12px);
  color: var(--neon-blue);
  font-weight: 800;
  background: rgba(0,188,212,0.25);
  padding: 6px 12px;
  border-radius: 12px;
  border: 1px solid rgba(0,188,212,0.5);
  box-shadow: 0 0 10px rgba(0,188,212,0.3);
  transition: all 0.3s ease;
}

.tile-rh:hover {
  background: rgba(0,188,212,0.4);
  box-shadow: 0 0 20px rgba(0,188,212,0.5);
  transform: scale(1.1) rotate(360deg);
}

.pagebar {
  height: 10vh;
  min-height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3vw;
  background: rgba(10,10,15,0.95);
  backdrop-filter: blur(25px);
  border-top: 1px solid rgba(255,255,255,0.15);
}

.page-pip {
  width: 10px; height: 10px;
  border-radius: 50%;
  background: rgba(255,255,255,0.5);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  cursor: pointer;
  position: relative;
}

.page-pip::before {
  content: '';
  position: absolute;
  inset: -5px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  opacity: 0;
  transition: all 0.3s ease;
}

.page-pip:hover {
  transform: scale(1.5);
  box-shadow: 0 0 20px rgba(255,255,255,0.6);
}

.page-pip:hover::before {
  opacity: 1;
  transform: scale(1.2);
}

.page-pip.active {
  background: var(--neon-red);
  box-shadow: 0 0 20px var(--neon-red);
  width: 14px; height: 14px;
  animation: pipPulse 1s infinite;
}

@keyframes pipPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

.timer-bar-wrap {
  width: clamp(140px, 18vw, 180px);
  height: 8px;
  background: rgba(255,255,255,0.15);
  border-radius: 5px;
  overflow: hidden;
}

.timer-bar {
  height: 100%;
  background: linear-gradient(90deg, var(--neon-red), var(--neon-yellow));
  border-radius: 5px;
  transition: width 1s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  box-shadow: 0 0 15px var(--neon-red);
  position: relative;
  overflow: hidden;
}

.timer-bar::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
  animation: timerShine 1s infinite;
}

@keyframes timerShine {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.timer-txt {
  font-family: 'Orbitron', monospace;
  font-size: clamp(12px, 1.6vw, 14px);
  font-weight: 800;
  color: rgba(255,255,255,1);
}

@media (max-width: 1200px) {
  .main-grid { grid-template-columns: 1fr; grid-template-rows: auto 1fr 1fr; }
  .big-stat { grid-row: 1; }
  .suhu-grid { grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(5, 1fr); }
  .product-name-cell { 
    max-width: 180px !important; 
  }
  .product-name-cell > div:last-child {
    font-size: 10px !important;
  }
  .time-info {
    flex-direction: column !important;
    gap: 1vh !important;
  }
  
  .time-start, .time-end, .time-running {
    min-width: auto !important;
  }
}
</style>

<div class="particles" id="particles"></div>

{{-- SCREEN 1: PRODUKSI --}}
<div class="screen active" id="screen-produksi">
  <div class="topbar">
    <div class="brand">RTM</div>
    <div class="screen-title">Ready Meal's Production Dashboard</div>
    <div class="topbar-right">
      <div class="live-pill">
        <div class="live-dot"></div>
        LIVE
      </div>
      <div class="clock-wrap">
        <div class="clock" id="clock1">--:--:--</div>
        <div class="clock-date" id="date1">---</div>
      </div>
    </div>
  </div>

  <div class="body-content">
    <div class="main-grid">
      <div class="big-stat glass-card">
        <div class="bs-label">Total Today</div>
        <div class="bs-num">{{ $totalCookingHariIni ?? 0 }}</div>
        <div class="bs-sub">Products</div>
      </div>

      <div class="prog-card glass-card">
        @if($lastCooking)
        @php
        $barCls = $statusCooking === 'Selesai' ? 'selesai' : 'proses';
        $badgeCls = $barCls;
        @endphp
        <div class="prog-top">
          <div class="prog-left">
            <h3>{{ $lastCooking->kode_produksi }}</h3>
            <div class="product-name">{{ $lastCooking->nama_produk }}</div>
            @if(isset($lastCooking->sub_produk) && $lastCooking->sub_produk)
            <div class="sub-info">Sub: {{ $lastCooking->sub_produk }}</div>
            @endif

            {{-- JAM MULAI & SELASAI BARU --}}
            <div class="time-info" style="margin-top:1vh; display:flex; gap:2vw; font-size:clamp(12px,1.3vw,13px); color:rgba(255,255,255,0.85);">
              <div class="time-start">
                <span style="color:var(--neon-green); font-weight:700;">START</span><br>
                {{ $lastCooking->waktu_mulai ?? '---' }}
              </div>
              @if($lastCooking->waktu_selesai)
              <div class="time-end">
                <span style="color:var(--neon-green); font-weight:700;">END</span><br>
                {{ $lastCooking->waktu_selesai }}
              </div>
              @else
              <div class="time-running" style="color:var(--neon-blue); font-weight:700; animation: blink 1.5s infinite;">
                <span style="color:var(--neon-blue);">● LIVE</span><br>
                Running...
              </div>
              @endif
            </div>
          </div>

          <div class="prog-right">
            <div class="prog-pct">{{ $progressCooking }}%</div>
            <div class="status-badge {{ $badgeCls }}">
              <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
              {{ $statusCooking === 'Selesai' ? 'Completed' : 'In Progress' }}
            </div>
          </div>
        </div>
        <div class="pbar-track">
          <div class="pbar-fill {{ $barCls }}" style="width:{{ $progressCooking }}%"></div>
        </div>
        @else
        <div class="no-active-state">
          <div class="no-active-title">No Active</div>
          <div class="no-active-subtitle">Production</div>
        </div>
        @endif
      </div>

      <div class="tbl-card glass-card">
        <div class="tbl-head">
          <div class="tbl-head-icon"></div>
          <div class="tbl-head-title">Today Production ({{ $jamCooking->count() }} Total)</div>
        </div>
        <div class="rolling-container">
          <div class="rolling-list">
            <table class="jam-table">
              <thead style="position:sticky;top:0;background:rgba(255,255,255,0.05);">
                <tr>
                  <th style="width:50%;">Product</th> {{-- DIPERLEBAR --}}
                  <th style="width:20%;">Code</th>
                  <th style="width:15%;">Start</th>
                  <th style="width:15%;">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($jamCooking as $row)
                <tr class="{{ $row->waktu_selesai ? '' : 'row-active' }}">
                  <td class="product-name-cell">
                    <div style="font-weight:700; line-height:1.3;">
                      {{ $row->nama_produk }}
                    </div>
                    @if($row->sub_produk && trim($row->sub_produk) !== '')
                    <div style="font-size:0.85em; color:var(--neon-blue); font-weight:600; margin-top:2px; letter-spacing:0.5px;">
                      {{ $row->sub_produk }}
                    </div>
                    @endif
                  </td>
                  <td class="code-cell">{{ $row->kode_produksi }}</td>
                  <td style="color:rgba(255,255,255,0.9);font-size:clamp(12px,1.4vw,13px);">
                    {{ $row->waktu_mulai ?? '-' }}
                  </td>
                  <td style="font-weight:800;">
                    @if($row->waktu_selesai)
                    <span style="color:var(--neon-green);">✓ COMPLETED</span>
                    @else
                    <span style="color:var(--neon-blue);">● RUNNING</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:rgba(255,255,255,0.5);">No production today</td></tr>
                @endforelse
              </tbody>
            </table>

            {{-- DUPLICATE untuk seamless rolling --}}
            <table class="jam-table" style="margin-top:2vh;">
              <thead style="display:none;"></thead>
              <tbody>
                @forelse($jamCooking as $row)
                <tr class="{{ $row->waktu_selesai ? '' : 'row-active' }}">
                  <td class="product-name-cell">
                    <div style="font-weight:700; line-height:1.3;">
                      {{ $row->nama_produk }}
                    </div>
                    @if($row->sub_produk && trim($row->sub_produk) !== '')
                    <div style="font-size:0.85em; color:var(--neon-blue); font-weight:600; margin-top:2px; letter-spacing:0.5px;">
                      {{ $row->sub_produk }}
                    </div>
                    @endif
                  </td>
                  <td class="code-cell">{{ $row->kode_produksi }}</td>
                  <td style="color:rgba(255,255,255,0.9);font-size:clamp(12px,1.4vw,13px);">
                    {{ $row->waktu_mulai ?? '-' }}
                  </td>
                  <td style="font-weight:800;">
                    @if($row->waktu_selesai)
                    <span style="color:var(--neon-green);">✓ COMPLETED</span>
                    @else
                    <span style="color:var(--neon-blue);">● RUNNING</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:rgba(255,255,255,0.5);">No production today</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="pagebar">
    <div class="page-pip active" id="pip1"></div>
    <div class="page-pip" id="pip2"></div>
    <div class="timer-bar-wrap">
      <div class="timer-bar" id="timer1" style="width:100%"></div>
    </div>
    <div class="timer-txt" id="ttxt1">30s</div>
  </div>
</div>

{{-- SCREEN 2: SUHU - FULLY FIXED --}}
<div class="screen" id="screen-suhu">
  <div class="topbar">
    <div class="brand">RTM</div>
    <div class="screen-title">Temperature Control</div>
    <div class="topbar-right">
      <div class="live-pill">
        <div class="live-dot"></div>
        LIVE
      </div>
      <div class="clock-wrap">
        <div class="clock" id="clock2">--:--:--</div>
        <div class="clock-date" id="date2">---</div>
      </div>
    </div>
  </div>

  <div class="body-content">
    <div class="suhu-grid glass-card" style="padding: 3vh;">

      @php
      // FIXED: Ambil data TERAKHIR dari collection
      $latestSuhuRaw = $data->isNotEmpty() ? $data->last() : null;

      // STANDAR SUHU
      $standar = [
      'chillroom'     => ['min' => 0,  'max' => 4],
      'cs_1'          => ['min' => -22, 'max' => -18],
      'cs_2'          => ['min' => -22, 'max' => -18],
      'anteroom_rm'   => ['min' => 8,  'max' => 10],
      'seasoning_suhu'=> ['min' => 22, 'max' => 30],
      'seasoning_rh'  => ['min' => 0,  'max' => 75],
      'prep_room'     => ['min' => 8,  'max' => 15],
      'cooking'       => ['min' => 20, 'max' => 30],
      'filling'       => ['min' => 20, 'max' => 30],
      'rice'          => ['min' => 20, 'max' => 30],
      'noodle'        => ['min' => 20, 'max' => 30],
      'topping'       => ['min' => 8,  'max' => 15],
      'packing'       => ['min' => 8,  'max' => 15],
      'ds_suhu'       => ['min' => 20, 'max' => 30],
      'ds_rh'         => ['min' => 0,  'max' => 75],
      'cs_fg'         => ['min' => -22,'max' => -18],
      'anteroom_fg'   => ['min' => 0,  'max' => 10],
      ];

      // Mapping field Suhu model ke nama room
      $suhuMapping = [
      'chillroom' => ['field' => 'chillroom', 'name' => 'CHILLROOM'],
      'cs_1' => ['field' => 'cs_1', 'name' => 'COLD STORE 1'],
      'cs_2' => ['field' => 'cs_2', 'name' => 'COLD STORE 2'],
      'anteroom_rm' => ['field' => 'anteroom_rm', 'name' => 'ANTEROOM RM'],
      'seasoning_suhu' => ['field' => 'seasoning_suhu', 'name' => 'SEASONING', 'rh_field' => 'seasoning_rh'],
      'prep_room' => ['field' => 'prep_room', 'name' => 'PREP ROOM'],
      'cooking' => ['field' => 'cooking', 'name' => 'COOKING AREA'],
      'filling' => ['field' => 'filling', 'name' => 'FILLING AREA'],
      'rice' => ['field' => 'rice', 'name' => 'RICE AREA'],
      'noodle' => ['field' => 'noodle', 'name' => 'NOODLE AREA'],
      'topping' => ['field' => 'topping', 'name' => 'TOPPING AREA'],
      'packing' => ['field' => 'packing', 'name' => 'PACKING AREA'],
      'ds_suhu' => ['field' => 'ds_suhu', 'name' => 'DRY STORAGE', 'rh_field' => 'ds_rh'],
      'cs_fg' => ['field' => 'cs_fg', 'name' => 'CS FG'],
      'anteroom_fg' => ['field' => 'anteroom_fg', 'name' => 'ANTEROOM FG'],
      ];
      @endphp

      {{-- DEBUG --}}
      <div style="position:absolute;top:10px;right:10px;font-size:10px;color:var(--neon-green);background:rgba(0,0,0,0.7);padding:8px;border-radius:8px;z-index:10;">
        Records: {{ $data->count() ?? 0 }}<br>
        Last: {{ $latestSuhuRaw->pukul ?? 'No data' }}
      </div>

      @foreach($suhuMapping as $key => $room)
      @php
      $tempVal = $latestSuhuRaw->{$room['field']} ?? null;
      $rhVal = isset($room['rh_field']) ? ($latestSuhuRaw->{$room['rh_field']} ?? null) : null;

      // CEK WARNING UNTUK TEMPERATURE
      $tempWarning = false;
      $tempCls = '';
      $tempDisplay = '--.-';

      if ($tempVal !== null && $tempVal !== '' && is_numeric($tempVal)) {
        $tempFloat = (float)$tempVal;
        $tempDisplay = number_format($tempFloat, 1);

        // CEK STANDAR TEMPERATURE
        if (isset($standar[$key])) {
          $std = $standar[$key];
          if ($tempFloat < $std['min'] || $tempFloat > $std['max']) {
            $tempWarning = true;
            $tempCls = 'tile-warning';
          } else {
            // Normal temperature classification
            if ($tempFloat > 50) $tempCls = 'tile-hot';
            elseif ($tempFloat > 20) $tempCls = 'tile-warm';
            elseif ($tempFloat < 5) $tempCls = 'tile-cold';
            else $tempCls = 'tile-cool';
          }
        }
      }

      // CEK WARNING UNTUK RH
      $rhWarning = false;
      $rhDisplay = null;
      if ($rhVal !== null && is_numeric($rhVal)) {
        $rhFloat = (float)$rhVal;
        $rhDisplay = number_format($rhFloat, 0);

        // CEK STANDAR RH
        $rhKey = $room['rh_field'] ?? null;
        if ($rhKey && isset($standar[$rhKey])) {
          $rhStd = $standar[$rhKey];
          if ($rhFloat < $rhStd['min'] || $rhFloat > $rhStd['max']) {
            $rhWarning = true;
          }
        }
      }

      // TOOLTIP dengan WARNING info
      $tooltip = $room['name'] . ': ' . $tempDisplay . '°C';
      if ($tempWarning) $tooltip .= ' ⚠️ OUT OF RANGE';
      if ($rhDisplay) {
        $tooltip .= ' | RH ' . $rhDisplay . '%';
        if ($rhWarning) $tooltip .= ' ⚠️';
      }
      $tooltip .= "\nRange: " . ($standar[$key]['min'] ?? '?') . '° - ' . ($standar[$key]['max'] ?? '?') . '°C';
      $tooltip .= "\nTime: " . ($latestSuhuRaw->pukul ?? 'No data');
      @endphp

      <div class="suhu-tile {{ $tempCls }} {{ $tempWarning ? 'tile-warning' : '' }}" 
      title="{{ $tooltip }}" 
      style="{{ $tempWarning ? 'animation: warningPulse 1s infinite;' : '' }}">
      <div class="tile-name">{{ $room['name'] }}</div>
      <div class="tile-temp {{ $tempWarning ? 'warning-temp' : '' }}">
        {{ $tempDisplay }}°C
      </div>
      @if($rhDisplay)
      <div class="tile-rh {{ $rhWarning ? 'warning-rh' : '' }}">
        RH {{ $rhDisplay }}%
      </div>
      @endif
    </div>
    @endforeach
  </div>
</div>

<div class="pagebar">
  <div class="page-pip" id="pip3"></div>
  <div class="page-pip active" id="pip4"></div>
  <div class="timer-bar-wrap">
    <div class="timer-bar" id="timer2" style="width:100%"></div>
  </div>
  <div class="timer-txt" id="ttxt2">30s</div>
</div>
</div>
<script>
// ULTRA INTERACTIVE Particles
  function createParticle() {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.left = Math.random() * 100 + '%';
    p.style.top = Math.random() * 100 + '%';
    p.style.width = p.style.height = (Math.random() * 4 + 1) + 'px';
    p.style.animationDelay = Math.random() * 6 + 's';
    p.style.animationDuration = (Math.random() * 3 + 4) + 's';
    p.style.setProperty('--color', `hsl(${Math.random()*360}, 70%, 60%)`);
    document.getElementById('particles').appendChild(p);
    return p;
  }

  for(let i = 0; i < 60; i++) createParticle();

// SUPER Interactive mouse trail
    let mouseX = 0, mouseY = 0;
  document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;

    document.querySelectorAll('.particle').forEach((p, i) => {
      const rect = p.getBoundingClientRect();
      const dx = mouseX - (rect.left + rect.width/2);
      const dy = mouseY - (rect.top + rect.height/2);
      const dist = Math.hypot(dx, dy);

      if (dist < 120) {
        p.classList.add('hover');
        const force = (120 - dist) / 120;
        p.style.transform = `translate(${dx/15}px, ${dy/15}px) scale(${1 + force})`;
        setTimeout(() => p.classList.remove('hover'), 300);
      }
    });
  });

// Click ripple effect
  document.addEventListener('click', (e) => {
    const ripple = document.createElement('div');
    ripple.style.cssText = `
    position: fixed;
    left: ${e.clientX}px;
    top: ${e.clientY}px;
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--neon-blue);
    pointer-events: none;
    z-index: 9999;
    transform: scale(0);
    animation: ripple 0.6s ease-out forwards;
    `;
    document.body.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });

  const style = document.createElement('style');
  style.textContent = `
  @keyframes ripple {
    to { transform: scale(20); opacity: 0; }
  }
  `;
  document.head.appendChild(style);

// Clock
  function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    const date = now.toLocaleDateString('id-ID', {weekday:'short', day:'numeric', month:'short'});
    [1,2].forEach(n => {
      document.getElementById(`clock${n}`).textContent = time;
      document.getElementById(`date${n}`).textContent = date;
    });
  }
  setInterval(updateClock, 1000);
  updateClock();

// Page Switcher
  let currentPage = 0;
  const PAGE_DURATION = 30000;
  let timeLeft = PAGE_DURATION / 1000;

  function switchPage() {
    const leavingScreen = document.querySelector('.screen.active');
    leavingScreen.classList.add('leaving');

    setTimeout(() => {
      currentPage = 1 - currentPage;
      document.querySelectorAll('.screen').forEach((s, i) => {
        s.classList.remove('active', 'leaving');
        if (i === currentPage) s.classList.add('active');
      });

      document.getElementById('pip1').classList.toggle('active', currentPage === 0);
      document.getElementById('pip2').classList.toggle('active', currentPage === 1);
      document.getElementById('pip3').classList.toggle('active', currentPage === 0);
      document.getElementById('pip4').classList.toggle('active', currentPage === 1);

      timeLeft = PAGE_DURATION / 1000;
    }, 400);
  }

// Manual page switch
  document.querySelectorAll('.page-pip').forEach((pip, index) => {
    pip.addEventListener('click', () => {
      if (currentPage !== index % 2) switchPage();
    });
  });

// Timer
  setInterval(() => {
    timeLeft--;
    const pct = (timeLeft / (PAGE_DURATION / 1000)) * 100;
    document.getElementById('timer1').style.width = pct + '%';
    document.getElementById('timer2').style.width = pct + '%';
    document.getElementById('ttxt1').textContent = Math.ceil(timeLeft) + 's';
    document.getElementById('ttxt2').textContent = Math.ceil(timeLeft) + 's';

    if (timeLeft <= 0) switchPage();
  }, 1000);

// Enhanced table interactions
  document.addEventListener('click', (e) => {
    const row = e.target.closest('.jam-table tr');
    if (row) {
      row.style.transform = 'scale(0.95) translateY(-5px)';
      setTimeout(() => {
        row.style.transform = '';
      }, 200);
    }
  });

// Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') {
      if (currentPage === 1) switchPage();
    } else if (e.key === 'ArrowRight') {
      if (currentPage === 0) switchPage();
    }
  });

// Auto refresh
  setInterval(() => location.reload(), 60000);
</script>

@endsection