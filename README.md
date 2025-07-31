# 寵物健康管理系統

寵物健康管理系統（PetCare System）是一款幫助寵物主人管理寵物健康記錄、行為日誌和多隻寵物的網路應用程式。採用 **Laravel 11** 後端與 **Vue 3 搭配 Pinia** 前端，通過 **Docker** 容器化部署，支援多語系（繁體中文、英文、簡體中文）和漸進式網頁應用（PWA）離線功能，提供安全、流暢的使用體驗。

## 專案亮點

- **安全認證**：使用 Laravel Sanctum 實現無狀態 API 認證，確保用戶數據安全。
- **多寵物管理**：支援用戶管理多隻寵物，一鍵切換預設寵物，提升操作效率。
- **多語系動態切換**：後端與前端整合，根據用戶請求動態切換繁體中文、英文和簡體中文。
- **PWA 離線體驗**：透過 Vite PWA 外掛實現離線訪問，類似原生應用體驗。
- **容器化部署**：Docker Compose 確保開發、測試和生產環境一致，支援外部 MySQL 資料庫。
- **模組化設計**：後端 RESTful API 和前端 Pinia 狀態管理，易於擴展新功能。

## 系統架構

- **後端**：
  - **框架**：Laravel 11（PHP 8.2）
  - **認證**：Laravel Sanctum（個人存取令牌）
  - **資料庫**：外部 MySQL，包含用戶、寵物、健康記錄和行為日誌表
  - **API**：RESTful 端點（`/api/pets`、`/api/health-records`、`/api/behavior-logs`）
  - **多語系**：Laravel 翻譯檔案，動態切換語系
  - **權限**：Policy 控制用戶對寵物和記錄的訪問

- **前端**：
  - **框架**：Vue 3、Pinia、Vue Router、Vue I18n
  - **狀態管理**：Pinia 管理認證和寵物狀態
  - **多語系**：Vue I18n 支援動態語系切換
  - **PWA**：Vite PWA 外掛，支援 Service Worker 和離線緩存

- **部署**：
  - **容器化**：Docker Compose（Nginx、Laravel、Vue 構建服務）
  - **反向代理**：Nginx 處理前端靜態檔案和後端 API 請求
  - **網路**：Docker 網路（`app-network`）確保服務間通信

## 前置條件

- PHP 8.2+ 和 Composer（用於 Laravel 依賴）
- Node.js 18+ 和 npm（用於 Vue 前端）
- Docker 和 Docker Compose（用於容器化部署）
- 外部 MySQL 資料庫（需提供主機地址、用戶名和密碼）
- Git（用於版本控制）

## 安裝步驟

1. **複製儲存庫**：
   ```bash
   git clone YOUR_GITHUB_REPO_URL
   cd petcare-system
   ```

2. **後端設定（Laravel）**：
   - 進入 `backend` 目錄：
     ```bash
     cd backend
     ```
   - 安裝依賴（需手動安裝）：
     ```bash
     composer install
     ```
   - 複製並配置 `.env`：
     ```bash
     cp .env.example .env
     ```
     修改 `backend/.env`：
     ```env
     DB_CONNECTION=mysql
     DB_HOST=your_external_mysql_host
     DB_PORT=3306
     DB_DATABASE=pet_health_manager
     DB_USERNAME=your_mysql_username
     DB_PASSWORD=your_mysql_password
     APP_URL=http://localhost:8080
     SANCTUM_STATEFUL_DOMAINS=localhost:8080,127.0.0.1:8080
     SESSION_DOMAIN=localhost
     ```
   - 生成金鑰並執行遷移：
     ```bash
     php artisan key:generate
     php artisan migrate
     ```

3. **前端設定（Vue）**：
   - 進入 `frontend` 目錄：
     ```bash
     cd frontend
     ```
   - 安裝依賴：
     ```bash
     npm install
     ```
   - 設定 `.env.development`：
     ```env
     VITE_API_BASE_URL=/api
     ```

4. **Docker 設定**：
   - 確保 `docker-compose.yml` 的 MySQL 配置正確。
   - 構建並啟動容器：
     ```bash
     docker compose up --build -d
     ```
   - 檢查服務狀態：
     ```bash
     docker compose ps
     ```

5. **訪問應用程式**：
   - 瀏覽器訪問 `http://localhost:8080`。
   - 註冊或登錄，管理寵物、健康記錄和行為日誌。

## 使用說明

- **註冊/登錄**：訪問 `/register` 或 `/login`，使用 Sanctum 認證。
- **管理寵物**：在 `/dashboard` 新增或切換寵物，管理健康記錄和行為日誌。
- **健康記錄**：新增疫苗、體檢記錄，包含日期、類型和描述。
- **行為日誌**：記錄行為、情緒和食慾，支援查看和編輯。
- **語言切換**：導航列下拉選單切換繁體中文、英文或簡體中文。
- **PWA**：安裝應用程式以支援離線訪問（需替換 `frontend/src/assets/icon-*.png`）。

## 關鍵程式碼

以下為系統核心程式碼，包含中文註解：

### 後端 - Pet 模型 (`app/Models/Pet.php`)
```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    // 可填充欄位，定義允許批量賦值的屬性
    protected $fillable = ['user_id', 'name', 'species', 'breed', 'date_of_birth', 'is_default'];

    // 將日期欄位轉換為日期格式
    protected $casts = [
        'date_of_birth' => 'date',
        'is_default' => 'boolean',
    ];

    // 定義與使用者的關聯（一隻寵物屬於一個使用者）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 定義與健康記錄的關聯（一隻寵物有多筆健康記錄）
    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    // 定義與行為日誌的關聯（一隻寵物有多筆行為日誌）
    public function behaviorLogs()
    {
        return $this->hasMany(BehaviorLog::class);
    }
}
```

### 後端 - 健康記錄控制器 (`app/Http/Controllers/HealthRecordController.php`)
```php
<?php
namespace App\Http\Controllers;
use App\Models\HealthRecord;
use App\Models\Pet;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    // 確保只有寵物主人能訪問記錄
    public function __construct()
    {
        $this->authorizeResource(HealthRecord::class, 'health_record');
    }

    // 獲取指定寵物的健康記錄
    public function indexByPet(Pet $pet)
    {
        $this->authorize('viewAny', [HealthRecord::class, $pet]);
        return $pet->healthRecords()->get();
    }

    // 新增健康記錄
    public function store(Request $request, Pet $pet)
    {
        $this->authorize('create', [HealthRecord::class, $pet]);
        $validated = $request->validate([
            'record_date' => 'required|date',
            'record_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'veterinarian' => 'nullable|string|max:255',
            'next_appointment' => 'nullable|date',
        ]);
        return $pet->healthRecords()->create($validated);
    }
}
```

### 前端 - 儀表板組件 (`frontend/src/views/DashboardView.vue`)
```vue
<template>
  <div class="container mx-auto p-4">
    <!-- 寵物選擇下拉選單 -->
    <select v-model="selectedPetId" @change="changePet" class="mb-4 p-2 border rounded">
      <option v-for="pet in pets" :key="pet.id" :value="pet.id">{{ pet.name }}</option>
    </select>

    <!-- 健康記錄表單 -->
    <form @submit.prevent="addHealthRecord">
      <input type="date" v-model="newRecord.record_date" required />
      <input type="text" v-model="newRecord.record_type" :placeholder="$t('health_record.type')" required />
      <button type="submit">{{ $t('health_record.add') }}</button>
    </form>

    <!-- 健康記錄列表 -->
    <ul>
      <li v-for="record in healthRecords" :key="record.id">
        {{ record.record_date }} - {{ record.record_type }}
      </li>
    </ul>
  </div>
</template>

<script>
import { usePetStore } from '@/stores/pet';
import axios from 'axios';

export default {
  data() {
    return {
      newRecord: { record_date: '', record_type: '' },
    };
  },
  computed: {
    pets() {
      return usePetStore().pets; // 從 Pinia 獲取寵物列表
    },
    selectedPetId: {
      get() {
        return usePetStore().selectedPetId; // 獲取當前選定寵物
      },
      set(value) {
        usePetStore().setSelectedPet(value); // 更新選定寵物
      },
    },
    healthRecords() {
      return usePetStore().healthRecords; // 獲取健康記錄
    },
  },
  methods: {
    async changePet() {
      await usePetStore().fetchHealthRecords(); // 切換寵物時更新健康記錄
    },
    async addHealthRecord() {
      await axios.post(`/api/pets/${this.selectedPetId}/health-records`, this.newRecord);
      await usePetStore().fetchHealthRecords(); // 新增後刷新記錄
      this.newRecord = { record_date: '', record_type: '' }; // 重置表單
    },
  },
};
</script>
```

### 前端 - Pinia 寵物狀態管理 (`frontend/src/stores/pet.js`)
```javascript
import { defineStore } from 'pinia';
import axios from 'axios';

export const usePetStore = defineStore('pet', {
  state: () => ({
    pets: [], // 儲存寵物列表
    selectedPetId: null, // 當前選定寵物 ID
    healthRecords: [], // 儲存健康記錄
    behaviorLogs: [], // 儲存行為日誌
  }),
  actions: {
    // 獲取用戶的寵物列表
    async fetchPets() {
      const response = await axios.get('/api/pets');
      this.pets = response.data;
      if (this.pets.length > 0 && !this.selectedPetId) {
        this.selectedPetId = this.pets.find(p => p.is_default)?.id || this.pets[0].id;
      }
    },
    // 設置選定寵物
    setSelectedPet(petId) {
      this.selectedPetId = petId;
    },
    // 獲取健康記錄
    async fetchHealthRecords() {
      const response = await axios.get(`/api/pets/${this.selectedPetId}/health-records`);
      this.healthRecords = response.data;
    },
  },
});
```
以下是針對寵物健康管理系統（PetCare System）的常見技術問題與解答，供技術人員參考。系統基於 Laravel 11（後端）、Vue 3 搭配 Pinia（前端），並使用 Docker 部署，支援多語系和 PWA 功能。

## 1. 系統如何實現用戶認證？
**解答**：系統使用 Laravel Sanctum 提供無狀態 API 認證。用戶通過 `/api/register` 和 `/api/login` 獲取個人存取令牌，儲存在前端 Pinia 的 `auth.js` 中。每次 API 請求攜帶 `Authorization: Bearer` 標頭，後端通過 `auth:sanctum` 中間件驗證身份。前端 `axios` 攔截器自動添加令牌並處理 401 錯誤，登出後清除令牌。

## 2. 如何管理多隻寵物？
**解答**：後端 `Pet` 模型與 `User` 模型建立一對多關聯（`user_id` 外鍵）。用戶可通過 `/api/pets` 端點新增、查看、更新和刪除寵物。`is_default` 欄位標記預設寵物，前端 Pinia 的 `pet.js` 儲存 `selectedPetId`，允許一鍵切換寵物並載入相關健康記錄和行為日誌。

## 3. 健康記錄和行為日誌如何與寵物關聯？
**解答**：`HealthRecord` 和 `BehaviorLog` 模型通過 `pet_id` 外鍵與 `Pet` 模型關聯（一對多）。後端控制器（`HealthRecordController` 和 `BehaviorLogController`）提供 RESTful 端點（`/api/pets/{pet}/health-records` 和 `/api/pets/{pet}/behavior-logs`），支援 CRUD 操作。前端 `DashboardView.vue` 通過 Pinia 動態載入選定寵物的記錄。

## 4. 如何實現多語系支援？
**解答**：後端使用 Laravel 的語系檔案（`resources/lang/{en,zh_TW,zh_CN}/messages.php`），通過 `SetLocale` 中間件根據 `Accept-Language` 標頭動態設定語系。前端使用 Vue I18n（`src/locales/{en,zh_TW,zh_CN}.json`），提供下拉選單切換語系，儲存用戶選擇於 `localStorage`，確保前後端語系一致。

## 5. PWA 功能是如何實現的？
**解答**：前端使用 Vite PWA 外掛（`vite-plugin-pwa`），配置於 `vite.config.js`，生成 `manifest.json` 和 Service Worker（`src/main.js`）。Nginx 配置（`nginx.conf`）支援 Service Worker 緩存策略，實現離線訪問。需手動替換 `frontend/src/assets/icon-*.png` 以完善 PWA 圖標。

## 6. Docker 部署的架構是什麼？
**解答**：系統使用 Docker Compose 定義三個服務：
- `nginx`：反向代理，處理前端靜態檔案和後端 API 請求。
- `backend`：Laravel 應用，運行 PHP-FPM，連接到外部 MySQL。
- `frontend_build`：Vue 構建服務，生成靜態檔案並傳遞給 Nginx。
所有服務通過 `app-network` 網路通信，確保環境一致性。

## 7. 如何確保資料安全？
**解答**：後端使用 Laravel Sanctum 驗證 API 請求，`PetPolicy`、`HealthRecordPolicy` 和 `BehaviorLogPolicy` 限制只有寵物擁有者能訪問數據。前端 `axios` 攔截器處理 CSRF 令牌並檢測 401 錯誤，自動登出無效會話。資料庫遷移使用外鍵約束（`onDelete('cascade')`）確保數據一致性。

## 8. 系統如何處理前端狀態管理？
**解答**：前端使用 Pinia（`src/stores/auth.js` 和 `pet.js`）管理認證和寵物狀態。`auth.js` 儲存用戶令牌和資訊，`pet.js` 管理寵物列表、選定寵物、健康記錄和行為日誌。狀態通過 `axios` 與後端 API 同步，支援非同步數據載入。

## 9. 如何擴展新功能（如飲食追蹤）？
**解答**：後端可新增模型（如 `FoodLog`）和控制器，定義新表結構並通過遷移檔案（`database/migrations`）創建。API 端點可擴展至 `routes/api.php`，前端新增對應組件和 Pinia 狀態。模組化設計（RESTful API 和 Pinia）確保擴展不影響現有功能。

## 10. 如何處理性能問題？
**解答**：當前系統未配置快取或佇列，建議引入 Redis（快取寵物和記錄數據）並使用 Laravel Queue 處理異步任務（如通知）。後端控制器可使用 `Cache::remember` 快取查詢結果，前端可通過 `keep-alive` 組件減少重複渲染。

## 注意事項

- **PWA 圖標**：請替換 `frontend/src/assets/icon-192x192.png` 和 `icon-512x512.png` 為實際圖標。
- **資料庫配置**：確保 `backend/.env` 和 `docker-compose.yml` 的 MySQL 連線資訊正確。
- **依賴安裝**：Laravel 依賴需手動安裝，腳本未包含自動化依賴安裝。
- **前端重建**：修改前端後需重新運行 `docker compose up --build -d`。
