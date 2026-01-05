---
name: auto-commit-sync
description: Stage changes, draft a Conventional Commit message, commit, pull --rebase, and push safely.
metadata:
  short-description: Auto stage + Conventional Commit + pull --rebase + push
---

# Auto Commit Sync

## Overview
Workflow aman dan repeatable buat commit + sync remote pakai Conventional Commits.

## Guardrails (wajib)
- Stop kalau bukan git repo: `git rev-parse --is-inside-work-tree`
- Stop kalau tidak ada perubahan: `git status --porcelain`
- Stop kalau detached HEAD: `git rev-parse --abbrev-ref HEAD` == `HEAD`
- Jika rebase conflict: stop, sebut file konflik, minta arahan.
- Jika push ditolak karena branch protection: sarankan feature branch lalu push ke sana.

## Steps (do in order)

### 1) Status + Diffstat
- `git status -sb`
- `git diff --stat`
- (kalau ada staged) `git diff --stat --cached`

### 2) Stage changes
- `git add -A`
- cek lagi:
  - `git status -sb`
  - `git diff --stat --cached`

### 3) Draft Conventional Commit message
- Tentukan `type(scope): summary` (scope opsional).
- Types: feat, fix, docs, refactor, test, chore, build, ci, perf, style, revert.
- Summary imperative, singkat, tanpa titik.
- Kalau perubahan campur-aduk dan nggak nyambung, minta user split.

(Optional)
- Tambahin body bullet points kalau perlu menjelaskan poin besar.

### 4) Run tests (optional, best-effort)
- Node: pilih berdasarkan lockfile -> `pnpm test` / `npm test` / `yarn test`
- Python: `pytest` kalau terdeteksi config yang relevan
- Kalau tidak ada command jelas: skip + laporkan.
- Kalau test gagal: stop dan laporkan (jangan commit) kecuali user minta tetap commit.

### 5) Commit
- `git commit -m "<type(scope): summary>"`

### 6) Pull --rebase lalu push
- `git pull --rebase`
- `git push`
- Jika upstream belum ada (push error): gunakan `git push -u origin <branch>`

### 7) Report
- `git log -1 --oneline`
- `git status -sb`
- Tampilkan commit message final yang dipakai.