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

## 注意事項

- **PWA 圖標**：請替換 `frontend/src/assets/icon-192x192.png` 和 `icon-512x512.png` 為實際圖標。
- **資料庫配置**：確保 `backend/.env` 和 `docker-compose.yml` 的 MySQL 連線資訊正確。
- **依賴安裝**：Laravel 依賴需手動安裝，腳本未包含自動化依賴安裝。
- **前端重建**：修改前端後需重新運行 `docker compose up --build -d`。
