@echo off
title Restaurant POS - Thermal Print Bridge
cd /d "%~dp0"
echo =========================================================
echo  Restaurant Operations - Thermal Print Bridge Daemon
echo =========================================================
echo Starting local print bridge...
node bridge.js
pause
