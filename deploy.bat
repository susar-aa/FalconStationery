@echo off
cd /d "%~dp0"
echo ===================================================
echo       GitHub Push ^& aaPanel Auto-Deploy Tool
echo ===================================================
echo.

set /p commitMessage="Enter commit message (or press enter for default): "
if "%commitMessage%"=="" set commitMessage=Automated deployment update

echo.
echo Adding changes...
git add .

echo.
echo Committing changes...
git commit -m "%commitMessage%"

echo.
echo Pushing to GitHub...
git push origin main

echo.
echo ===================================================
echo Success! Changes have been pushed to GitHub.
echo If your aaPanel webhook is set up correctly, 
echo the server will now automatically pull these files.
echo ===================================================
pause
