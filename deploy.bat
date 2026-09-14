@echo off
cd /d "%~dp0"
echo ===================================================
echo       GitHub Push ^& aaPanel Auto-Deploy Tool
echo ===================================================
echo.

set /p commitMessage="Enter commit message (or press enter for default): "
if "%commitMessage%"=="" set commitMessage=Automated deployment update

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
pause
