@echo off
cd /d "%~dp0"
echo ===================================================
echo       GitHub Push ^& aaPanel Auto-Deploy Tool
echo ===================================================
echo.

set /p commitMessage="Enter commit message (or press enter for summary): "
if "%commitMessage%"=="" (
    setlocal enabledelayedexpansion
    set "fileList="
    for /f "tokens=1,* delims= " %%i in ('git status --short') do (
        if not "%%j"=="" (
            set "filename=%%j"
            set "filename=!filename:\=/!"
            if defined fileList (
                set "fileList=!fileList!, !filename!"
            ) else (
                set "fileList=!filename!"
            )
        )
    )
    if "!fileList!"=="" (
        set "commitMessage=Update [%date% %time%]"
    ) else (
        set "commitMessage=Update: !fileList!"
    )
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
git commit -m "!commitMessage!" >> %LOGFILE% 2>&1

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
