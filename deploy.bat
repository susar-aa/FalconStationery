@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"

:: Auto-detect git executable if not present in PATH
where git >nul 2>&1
if errorlevel 1 (
    for /d %%D in ("%LOCALAPPDATA%\GitHubDesktop\app-*") do (
        if exist "%%D\resources\app\git\cmd\git.exe" set "PATH=%%D\resources\app\git\cmd;!PATH!"
    )
    if exist "C:\Program Files\Git\cmd\git.exe" set "PATH=C:\Program Files\Git\cmd;!PATH!"
    if exist "C:\Program Files (x86)\Git\cmd\git.exe" set "PATH=C:\Program Files (x86)\Git\cmd;!PATH!"
)

echo ===================================================
echo       GitHub Push ^& aaPanel Auto-Deploy Tool
echo ===================================================
echo.

set /p commitMessage="Enter commit message (or press enter for summary): "
if "!commitMessage!"=="" (
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
echo Executing local schema_update.sql if pending...
if exist schema_update.sql (
    c:\xampp\php\php.exe -r "$f='schema_update.sql'; if(file_exists($f)){ $sql=trim(preg_replace('/--.*$/m','',file_get_contents($f))); if(!empty($sql)){ require_once 'config/database.php'; require_once 'core/Database.php'; try { $db=new Database(); foreach(array_filter(array_map('trim',explode(';',$sql))) as $s){ if(!empty($s)){ $db->query($s); $db->execute(); } } echo \"Local DB Migration Complete.\n\"; } catch(Exception $e){ echo \"Local DB Migration Error: \".$e->getMessage().\"\n\"; } } file_put_contents($f, \"-- Copy ^& paste SQL queries here before running deploy.bat\n-- After deployment, the server will execute these queries once and automatically wipe this file clean.\n\"); }"
)

echo.
echo ===================================================
echo Success! Changes have been pushed to GitHub.
echo If your aaPanel webhook is set up correctly,
echo the server will now automatically pull these files.
echo Check deploy_log.txt for detailed output.
echo ===================================================
echo =================================================== >> %LOGFILE%
exit /b 0
