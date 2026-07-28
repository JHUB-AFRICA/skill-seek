# AGENTS.md

Purpose
-------
This file gives concise, actionable guidance for AI coding agents to validate the PHP executable on developer machines (Windows/XAMPP) so automated checks, scripts, or quick tasks run reliably.

Quick links
-----------
- Project DB/config helper: [config/database.php](config/database.php)

Validation checklist (what the agent should do)
---------------------------------------------
1. Check whether `php` is available on PATH:
   - CMD: run `where php`
   - PowerShell: run `Get-Command php | Select-Object -ExpandProperty Source`
2. If not found on PATH, check common XAMPP path: `C:\xampp\php\php.exe`.
3. If a candidate binary is found, verify it's executable and report its version:
   - `php -v` (should print the PHP version and build info)
   - `php -r "echo PHP_BINARY;"` (returns the binary path used)
4. Check PHP version expectations: prefer PHP 7.4+ for modern compatibility. If the project has an explicit requirement (e.g., `composer.json`), respect that.

Windows-specific notes
----------------------
- On Windows with XAMPP, the PHP binary is typically at `C:\xampp\php\php.exe`.
- If `php` exists but points to a different installation, prefer the one that matches the project's expected version.
- To add XAMPP PHP to PATH (PowerShell example):

  $old = [Environment]::GetEnvironmentVariable('Path', 'User')
  [Environment]::SetEnvironmentVariable('Path', "$old;C:\\xampp\\php", 'User')

Agent behaviour and messaging (concise)
-------------------------------------
- Success: report `PHP found: <path> (vX.Y.Z)` and proceed.
- Warning: `PHP found but version < recommended` — include version and recommend upgrade.
- Failure: `PHP executable not found` — include actionable fixes:
  - Install XAMPP or PHP and add the PHP folder to PATH.
  - Or set an explicit `PHP_PATH` environment variable pointing to `php.exe` and document it in this repository.

Examples the agent can show to the developer
-------------------------------------------
- Check PATH (CMD): `where php`
- Check PATH (PowerShell): `Get-Command php | Select-Object -ExpandProperty Source`
- Get version: `php -v`
- Get binary path: `php -r "echo PHP_BINARY;"`

If you need to update instructions
---------------------------------
Keep this file minimal. Link to external or project docs rather than duplicating them. If the project adopts a specific PHP version or a CI-based PHP setup, add a short note linking to that config.

End
