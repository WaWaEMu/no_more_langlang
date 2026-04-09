# 諾摩浪浪 — 開發規範 (RULES.md)

> 本文件記錄了此專案的程式碼風格、架構慣例與測試規範，供 AI 輔助工具與開發者遵循。

---

## 技術棧

| 層級 | 技術 |
|------|------|
| **後端** | Laravel 10 (PHP 8.1+) |
| **前端** | Vue 3 (Composition API) + TypeScript |
| **建置** | Vite 5 + laravel-vite-plugin |
| **狀態管理** | Pinia (Composition API 風格) |
| **路由** | vue-router 4 (SPA, `createWebHistory`) |
| **UI** | Bootstrap 5 + Bootstrap Icons |
| **國際化** | laravel-vue-i18n (`$t()` / `trans()`) |
| **認證** | Laravel Sanctum (Cookie-based SPA) |
| **通知** | SweetAlert2 |
| **圖片裁切** | vue-cropper |
| **測試** | PHPUnit (Feature + Unit) |

---

## 一、後端架構

### 1.1 分層模式

```
Controller → Service → Model
```

- **Controller**：僅處理 HTTP 請求/回應，呼叫 Service 處理業務邏輯。
- **Service**：封裝所有業務邏輯，透過 **Interface（位於 `app/Contracts/`）** 進行依賴注入。
- **Model**：負責資料存取、關聯定義與 Scope 查詢。

### 1.2 Service 與 Interface 規範

- 每個 Service 應有對應的 Interface，放在 `app/Contracts/` 目錄。
- 命名規則：`XxxService.php` ↔ `XxxServiceInterface.php`。
- 在 `AppServiceProvider` 中進行綁定。

```php
// app/Contracts/PetServiceInterface.php
interface PetServiceInterface { ... }

// app/Services/PetService.php
class PetService implements PetServiceInterface { ... }

// app/Providers/AppServiceProvider.php
$this->app->bind(PetServiceInterface::class, PetService::class);
```

### 1.3 Form Request 驗證

- 驗證邏輯一律寫在 `app/Http/Requests/` 目錄下的 Form Request 類別中。
- **必須** 覆寫 `messages()` 方法，提供正體中文的錯誤訊息。
- **必須** 覆寫 `attributes()` 方法，提供欄位顯示名稱。

### 1.4 Model 規範

- 善用 `const` 定義狀態常數：
  ```php
  public const STATUS_AVAILABLE = 'available';
  public const STATUS_ADOPTED = 'adopted';
  ```
- 透過 `scopeXxx` 定義可複用的查詢：
  ```php
  public function scopeFilter($query, array $filters) { ... }
  ```
- 關聯方法使用有意義的命名：`hasMany`、`belongsTo`、`hasOne`。

### 1.5 API 回應格式

- 成功回應：
  ```json
  { "success": true, "data": { ... } }
  ```
- 錯誤回應：
  ```json
  { "success": false, "message": "錯誤描述" }
  ```

### 1.6 路由規範

- **Web 路由** (`routes/web.php`)：僅處理 SPA 入口、SEO、認證回調。最後一條是 SPA fallback。
- **API 路由** (`routes/api.php`)：使用 RESTful 風格，需認證的路由包在 `auth:sanctum` middleware group 中。
- API 端點命名使用 kebab-case：`/adoption-cases`、`/diary-entries`。

---

## 二、Clean Code 規範 (PHP)

### 2.1 建構子屬性注入

採用 **PHP 8.1+ Constructor Property Promotion** 與 `readonly` 減少樣板程式碼。

```php
// ❌ 舊寫法（不推薦）
class AdoptionCaseService
{
    protected $notificationService; // 未型別提示，未 readonly

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
}

// ✅ 新寫法（推薦）
class AdoptionCaseService
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}
}
```

### 2.2 方法長度

- 單一方法不應超過 **25 行**。
- 過長的方法應拆分為私有輔助方法，命名以動詞開頭，如 `validateOwnership()`、`buildTrackingConfig()`。

### 2.3 避免 Magic Number 與裸字串

```php
// ❌
if ($images->count() >= 3) { ... }

// ✅
const MAX_PET_IMAGES = 3;
if ($images->count() >= self::MAX_PET_IMAGES) { ... }
```

### 2.4 Early Return（衛語句）

避免深層巢狀，優先使用 Early Return：

```php
// ❌
public function replaceImage(Pet $pet, int $imageId, $file)
{
    if ($pet->user_id === auth()->id()) {
        $image = $pet->images()->find($imageId);
        if ($image) {
            // 真正的邏輯
        }
    }
}

// ✅
public function replaceImage(Pet $pet, int $imageId, $file): void
{
    if ($pet->user_id !== auth()->id()) {
        throw new AuthorizationException();
    }

    $image = $pet->images()->findOrFail($imageId);
    // 真正的邏輯
}
```

### 2.5 Return Type Declaration

所有 `public` 方法**必須**宣告回傳型別：

```php
// ❌
public function getUserCases(User $user, string $role)

// ✅
public function getUserCases(User $user, string $role): Collection
```

---

## 三、Clean Code 規範 (Vue / TypeScript)

### 3.1 元件拆分原則

- 單一元件不超過 **300 行**（template + script + style 合計）。
- 若邏輯複雜，抽取至 `composables/useXxx.ts`；若是通用 UI，抽取至新元件。

### 3.2 命名規格

| 類型 | 範例 |
|------|------|
| 元件 | `PascalCase`：`PetCard.vue`, `AdoptionFormBuilder.vue` |
| 函式/方法 | `camelCase`：`handleSubmit`, `fetchPets` |
| 事件處理 | 前綴 `handle`：`handleConfirm`, `handleDelete` |
| 布林變數 | 前綴 `is/has/can`：`isLoading`, `hasError`, `canEdit` |
| Composable | 前綴 `use`：`useManualCaseCreation` |

### 3.3 Props 與 Emits 的型別

- `defineProps` 與 `defineEmits` **必須**使用 TypeScript 泛型語法，不使用 runtime 宣告：

```ts
// ❌
defineProps({ pet: Object, editable: Boolean })

// ✅
const props = defineProps<{
    pet: PetInter
    editable?: boolean
}>()

const emit = defineEmits<{
    (e: 'updated', pet: PetInter): void
}>()
```

### 3.4 Async/Await 錯誤處理

- 所有 `async` 函式的 API 呼叫**必須**包在 `try/catch/finally` 中。
- `finally` 用於重設 `isLoading` 等狀態，確保 UI 不會卡住。

```ts
// ✅
async function handleSubmit() {
    isLoading.value = true
    try {
        await api.post('/...')
        // success feedback
    } catch (error: any) {
        // error feedback via Swal
    } finally {
        isLoading.value = false
    }
}
```

### 3.5 避免 v-html 未消毒輸入

- 僅對**確定來自語系檔（i18n）**的字串使用 `v-html`，禁止對任何使用者輸入使用 `v-html`。

---

## 四、測試規範

### 4.1 測試類型

| 類型 | 目的 | 存放位置 |
|------|------|------|
| **Feature Test** | 測試 HTTP 端點的完整請求/回應流程 | `tests/Feature/` |
| **Unit Test** | 測試單一 Service/Model 方法的邏輯 | `tests/Unit/` |

### 4.2 命名規範

- 測試方法名稱以 **`it_`** 或 **`test_`** 前綴開頭，使用 snake_case，要能讓人讀懂在測試什麼：
  ```php
  // ❌
  public function test1() { ... }

  // ✅
  public function test_owner_can_finalize_adoption_and_create_case() { ... }
  public function it_returns_403_when_non_owner_tries_to_finalize() { ... }
  ```

### 4.3 Arrange-Act-Assert (AAA) 模式

每個測試方法**必須**遵守 AAA 結構，並以註解分隔：

```php
public function test_attach_sendable_cities_to_pet(): void
{
    /* Arrange */
    $pet = Pet::factory()->create();

    /* Act */
    $pet->attachSendableCities(['台北市', '新北市']);

    /* Assert */
    $this->assertDatabaseHas('pet_sendable_cities', [
        'pet_id' => $pet->id,
        'city'   => '台北市',
    ]);
}
```

### 4.4 必要的測試場景

新增或修改以下類型功能時，對應的測試為**必要的（非選項）**：

| 功能類型 | 應涵蓋的測試場景 |
|------|------|
| **API 端點** | 成功案例 (2xx)、未授權 (401)、禁止存取 (403)、資料驗證失敗 (422) |
| **Service 方法** | 正常輸入的業務邏輯結果、邊界條件（如空陣列、上限值） |
| **Model 方法** | 關聯是否正確寫入資料庫 (`assertDatabaseHas`) |
| **權限控制** | 「應能存取者」與「不應能存取者」各一個測試 |

### 4.5 測試工具規範

- **一律使用 `RefreshDatabase`**：確保每個測試都在乾淨的資料庫環境中執行。
- **使用 `Storage::fake('public')`**：測試涉及檔案上傳時，依此隔離真實磁碟。
- **使用 Factory 建立測試資料**：禁止在測試中直接 `new Model()`，一律透過 `ModelName::factory()->create()`。
- **使用 `actingAs($user)`**：測試需要認證的端點，避免直接操作 session。

### 4.6 執行測試

```bash
# 執行所有測試
php artisan test

# 僅執行 Feature 測試
php artisan test --testsuite=Feature

# 執行單一測試檔案
php artisan test tests/Feature/PetTest.php

# 執行符合條件的測試方法 (--filter)
php artisan test --filter test_owner_can_finalize_adoption
```

---

## 五、國際化 (i18n)

- 翻譯檔案位於 `lang/zh_TW.json`，為正體中文。
- **所有使用者可見的文字** 必須使用翻譯函式：
  - Vue 模板中：`{{ $t('key') }}`
  - Script 中：`import { trans } from 'laravel-vue-i18n'`，使用 `trans('key')`。
  - 後端中：`__('key')` 或 `__('key', ['param' => $value])`。
- 翻譯 Key 的命名慣例：
  - 簡短通用字串：`"Login"`、`"Cancel"`、`"Delete"`
  - 帶有命名空間：`"landing.Hero.Title"`、`"case.StepRole"`、`"diary.AddEntry"`
  - SEO 專用：`"SEO Default Title"`、`"SEO Pet Title"`

---

## 六、SEO 策略

- **SPA SEO** 由後端 `SeoController` 在渲染 `welcome.blade.php` 時注入 meta 與 JSON-LD schema。
- 動態 meta 支援：`title`、`description`、`image`、`image_alt`、`url`。
- 結構化資料包含：`Organization`、`WebSite`、`HowTo`、`BreadcrumbList`、`Product`。
- `sitemap.xml` 由 `SitemapController` 動態生成並快取 24 小時。
- `robots.txt` 由 `RobotsTxtController` 動態產生。

---

## 七、CSS 命名

- 使用 **BEM-like** 命名，以元件名稱作為 Block：
  ```css
  .case-detail__hero { ... }
  .case-detail__pet-image { ... }
  .case-detail__thumb-wrapper--active { ... }
  ```
- 配合 Bootstrap 5 的工具類別（`d-flex`、`gap-2`、`rounded-4` 等）。
- 主題色透過 CSS 變數控制：`var(--color-denim-blue)`。
- 按鈕圓角全站統一使用 **`border-radius: 8px`**，禁止使用百分比或膠囊型（50px）除非有特別設計需求。

---

## 八、Git 提交規範

使用 **Conventional Commits** 格式：

```
<type>(<scope>): <description>
```

常用類型：
- `feat`：新功能
- `fix`：修復錯誤
- `refactor`：重構（非功能變更）
- `style`：程式碼格式（不影響功能）
- `docs`：文件變更
- `test`：新增或修改測試

常用 scope：`seo`、`auth`、`adopt`、`case`、`ui`、`legal`、`test`

範例：
```
feat(adopt): add photo addition API and refactor image operations to PetService
test(adopt): add feature test for pet image replacement API
fix(auth): resolve login redirect loop
```

---

## 九、使用者通知 (UX)

- 操作成功/失敗的回饋一律使用 **SweetAlert2**。
- 確認操作（刪除、送出）使用 `Swal.fire` 的 confirm 模式。
- 風格統一：
  ```ts
  Swal.fire({
      icon: 'success',
      title: trans('成功標題'),
      text: trans('成功訊息'),
      confirmButtonColor: '#2c5282',
  })
  ```

---

## 十、注意事項

1. **不要直接在 Controller 中寫業務邏輯**，一律委派給 Service。
2. **不要在前端硬編碼中文字串**，一律使用 `$t()` 或 `trans()`。
3. **圖片上傳上限為 3 張**，單張最大 5MB。
4. **API 路由需認證的端點** 必須包在 `auth:sanctum` middleware 中。
5. **Vue 元件的 `<img>` 標籤** 必須提供有意義的 `alt` 屬性。
6. **新增頁面時**，記得同步更新 `router.ts`、`permission.ts` 白名單（若為公開頁面）及 `sitemap.blade.php`。
7. **所有 public Service 方法** 必須宣告 Return Type，使用 PHP 8.1+ Constructor Property Promotion。
8. **新功能若影響核心 API 端點，必須附帶對應的 Feature Test**，無測試的 PR 視為不完整。
