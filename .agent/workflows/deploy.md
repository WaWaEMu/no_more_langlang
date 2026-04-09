---
description: 部署最新程式碼到 Cloudways 正式機
---

## 部署流程

// turbo-all

1. 在專案根目錄執行部署腳本：
```bash
bash deploy.sh
```

此腳本會自動依序執行：
- `npm run build` 建構前端
- `git push origin master` 推送至 GitHub
- SSH 至 Cloudways 正式機拉取最新程式碼
- `composer install`、`php artisan migrate`、快取重建
