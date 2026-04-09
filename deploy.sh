#!/bin/bash
set -e

# ========================================
# 諾摩浪浪 — 一鍵部署腳本
# ========================================
# 流程：本地編譯 → 提交編譯產物 → 推送 → 正式機同步
# ========================================

REMOTE="origin"
BRANCH="master"
SERVER_USER="wawaemu"
SERVER_IP="104.248.154.177"
SSH_KEY="$HOME/.ssh/id_ecdsa"

echo ""
echo "🚀 開始部署流程..."
echo "========================================"

# Step 1: 本地建構前端 (使用正式機網址)
echo ""
echo "📦 Step 1/5: 建構前端資源 (Target: https://nomorelanglang.com)..."
APP_URL=https://nomorelanglang.com \
VITE_APP_URL=https://nomorelanglang.com \
VITE_FRONTEND_URL=https://nomorelanglang.com \
npm run build
echo "✅ 前端建構完成"

# Step 2: 將編譯產物加入 Git
echo ""
echo "📝 Step 2/5: 提交編譯後的前端資源..."
git add public/build/
if git diff --cached --quiet -- public/build/; then
    echo "⏭️  前端資源無變更，跳過提交"
else
    git commit -m "build: update compiled frontend assets"
    echo "✅ 編譯產物已提交"
fi

# Step 3: Push 到 Git Server
echo ""
echo "📤 Step 3/5: 推送到 Git Server..."
git push $REMOTE $BRANCH
echo "✅ 已推送至 $REMOTE/$BRANCH"

# Step 4: SSH 到正式機執行部署
echo ""
echo "🔄 Step 4/5: SSH 連線至正式機進行部署..."
ssh -i $SSH_KEY $SERVER_USER@$SERVER_IP << 'DEPLOY'
    cd /home/master/applications/sckvaxpwam/public_html
    set -e

    # 解決 Git Dubious Ownership 安全警告
    git config --global --add safe.directory /home/1276183.cloudwaysapps.com/sckvaxpwam/public_html || true

    echo "  → 強制同步遠端分支 (Fetch & Reset)..."
    git fetch origin master
    git reset --hard origin/master

    echo "  → 安裝 Composer 套件 (Production)..."
    composer install --no-interaction --no-dev --optimize-autoloader

    echo "  → 執行資料庫遷移..."
    php artisan migrate --force

    echo "  → 清除並重建快取..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    echo "  ✅ 正式機部署完成"
DEPLOY

# Step 5: 完成
echo ""
echo "========================================"
echo "🎉 部署完成！正式機已更新至最新版本"
echo "========================================"
echo ""
