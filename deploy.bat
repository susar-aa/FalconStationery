@echo off
cd /d "%~dp0"
echo ===================================================
echo       GitHub Push ^& aaPanel Auto-Deploy Tool
echo ===================================================
echo.

set /p commitMessage="Enter commit message (or press enter for default): "
if "%commitMessage%"=="" (
    for /f "tokens=1-3 delims=/ " %%a in ("%date%") do set mydate=%%c-%%a-%%b
    for /f "tokens=1-2 delims=: " %%a in ("%time%") do set mytime=%%a:%%b
    set commitMessage=Auto update [%date% %time%]
)

set LOGFILE=deploy_log.txt
echo =================================================== >> %LOGFILE%
echo Deployment Log - %date% %time% >> %LOGFILE%
echo =================================================== >> %LOGFILE%

echo.
echo Adding changes...
echo Adding changes... >> %LOGFILE%
git add . >> %LOGFILE% 2>&1

echo.
echo Committing changes...
echo Committing changes... >> %LOGFILE%
git commit -m "%commitMessage%" >> %LOGFILE% 2>&1

echo.
echo Pushing to GitHub...
echo Pushing to GitHub... >> %LOGFILE%
git push origin main >> %LOGFILE% 2>&1
type %LOGFILE%

echo.
echo ===================================================
echo Success! Changes have been pushed to GitHub.
echo If your aaPanel webhook is set up correctly,
echo the server will now automatically pull these files.
echo Check deploy_log.txt for detailed output.
echo ===================================================
echo =================================================== >> %LOGFILE%
exit /b 0
