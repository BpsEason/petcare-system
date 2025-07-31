<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '../stores/auth'; // 引入 auth store
import { usePetStore } from '../stores/pet';   // 引入 pet store

const router = useRouter();
const { t } = useI18n();
const authStore = useAuthStore();
const petStore = usePetStore();

const newPetName = ref('');
const newPetSpecies = ref('');
const newPetBreed = ref('');
const newPetDOB = ref('');

const newRecordDate = ref('');
const newRecordType = ref('');
const newRecordDescription = ref('');
const newRecordVeterinarian = ref('');
const newRecordNextDueDate = ref('');

const newLogDate = ref('');
const newLogBehavior = ref('');
const newLogEmotion = ref('');
const newLogAppetite = ref('');
const newLogNotes = ref('');

const errorMessage = ref('');

// 當 selectedPetId 改變時，重新載入健康記錄和行為日誌
watch(() => petStore.selectedPetId, (newVal) => {
  if (newVal) {
    petStore.fetchRecordsAndLogs(newVal);
  }
}, { immediate: true }); // immediate: true 確保組件初始化時執行一次

const addPet = async () => {
  errorMessage.value = '';
  try {
    const message = await petStore.addPet({
      name: newPetName.value,
      species: newPetSpecies.value,
      breed: newPetBreed.value,
      date_of_birth: newPetDOB.value,
    });
    newPetName.value = '';
    newPetSpecies.value = '';
    newPetBreed.value = '';
    newPetDOB.value = '';
    alert(message || t('pet_added'));
  } catch (error) {
    errorMessage.value = error.response?.data?.message || t('add_pet_failed');
    console.error('Add pet error:', error);
  }
};

const addHealthRecord = async () => {
  errorMessage.value = '';
  if (!petStore.selectedPetId) {
    errorMessage.value = t('select_pet_first');
    return;
  }
  try {
    const message = await petStore.addHealthRecord({
      pet_id: petStore.selectedPetId,
      record_date: newRecordDate.value,
      type: newRecordType.value,
      description: newRecordDescription.value,
      veterinarian: newRecordVeterinarian.value,
      next_due_date: newRecordNextDueDate.value,
    });
    newRecordDate.value = '';
    newRecordType.value = '';
    newRecordDescription.value = '';
    newRecordVeterinarian.value = '';
    newRecordNextDueDate.value = '';
    alert(message || t('health_record_added'));
  } catch (error) {
    errorMessage.value = error.response?.data?.message || t('add_record_failed');
    console.error('Add record error:', error);
  }
};

const addBehaviorLog = async () => {
  errorMessage.value = '';
  if (!petStore.selectedPetId) {
    errorMessage.value = t('select_pet_first');
    return;
  }
  try {
    const message = await petStore.addBehaviorLog({
      pet_id: petStore.selectedPetId,
      log_date: newLogDate.value,
      behavior: newLogBehavior.value,
      emotion: newLogEmotion.value,
      appetite: newLogAppetite.value,
      notes: newLogNotes.value,
    });
    newLogDate.value = '';
    newLogBehavior.value = '';
    newLogEmotion.value = '';
    newLogAppetite.value = '';
    newLogNotes.value = '';
    alert(message || t('behavior_log_added'));
  } catch (error) {
    errorMessage.value = error.response?.data?.message || t('add_behavior_log_failed');
    console.error('Add behavior log error:', error);
  }
};

onMounted(() => {
  if (authStore.isAuthenticated) {
    authStore.fetchUser();
    petStore.fetchPets();
  } else {
    router.push('/login');
  }
});
</script>

<template>
  <div class="dashboard-container">
    <h2>{{ t('dashboard') }}</h2>
    <p v-if="authStore.user">{{ t('welcome_user', { name: authStore.userName }) }}</p>

    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

    <hr />

    <h3>{{ t('current_pets') }}</h3>
    <form @submit.prevent="addPet" class="form-section">
      <h4>{{ t('add_new_pet') }}</h4>
      <div class="form-group">
        <label for="pet-name">{{ t('pet_name') }}：</label>
        <input type="text" id="pet-name" v-model="newPetName" required />
      </div>
      <div class="form-group">
        <label for="pet-species">{{ t('species') }}：</label>
        <input type="text" id="pet-species" v-model="newPetSpecies" required />
      </div>
      <div class="form-group">
        <label for="pet-breed">{{ t('breed') }}：</label>
        <input type="text" id="pet-breed" v-model="newPetBreed" />
      </div>
      <div class="form-group">
        <label for="pet-dob">{{ t('date_of_birth') }}：</label>
        <input type="date" id="pet-dob" v-model="newPetDOB" />
      </div>
      <button type="submit">{{ t('add_pet_btn') }}</button>
    </form>

    <div class="list-section">
      <h4>{{ t('current_pets') }}</h4>
      <p v-if="petStore.pets.length === 0">{{ t('no_pets') }}</p>
      <ul>
        <li v-for="pet in petStore.pets" :key="pet.id" :class="{ 'selected-pet': pet.id === petStore.selectedPetId }">
          {{ pet.name }} ({{ pet.species }}) - {{ pet.breed || 'N/A' }}
          <div class="pet-actions">
            <button @click="petStore.setSelectedPet(pet.id)">{{ t('view_records_btn') }}</button>
            <button @click="petStore.setSelectedPet(pet.id)">{{ t('view_behavior_logs_btn') }}</button>
          </div>
        </li>
      </ul>
    </div>

    <hr />

    <div v-if="petStore.selectedPetId">
      <p><strong>{{ t('current_selected_pet') }}</strong> {{ petStore.selectedPet?.name }}</p>

      <h3>{{ t('pet_health_records') }}</h3>
      <form @submit.prevent="addHealthRecord" class="form-section">
        <h4>{{ t('add_new_health_record') }}</h4>
        <div class="form-group">
          <label for="record-date">{{ t('record_date') }}：</label>
          <input type="date" id="record-date" v-model="newRecordDate" required />
        </div>
        <div class="form-group">
          <label for="record-type">{{ t('type') }}：</label>
          <input type="text" id="record-type" v-model="newRecordType" :placeholder="t('example_type')" required />
        </div>
        <div class="form-group">
          <label for="record-veterinarian">{{ t('veterinarian') }}：</label>
          <input type="text" id="record-veterinarian" v-model="newRecordVeterinarian" />
        </div>
        <div class="form-group">
          <label for="record-next-due-date">{{ t('next_due_date') }}：</label>
          <input type="date" id="record-next-due-date" v-model="newRecordNextDueDate" />
        </div>
        <div class="form-group">
          <label for="record-desc">{{ t('description') }}：</label>
          <textarea id="record-desc" v-model="newRecordDescription"></textarea>
        </div>
        <button type="submit">{{ t('add_record_btn') }}</button>
      </form>

      <div class="list-section">
        <h4>{{ t('current_records') }}</h4>
        <p v-if="petStore.healthRecords.length === 0">{{ t('no_health_records') }}</p>
        <ul>
          <li v-for="record in petStore.healthRecords" :key="record.id">
            {{ t('date_label') }}: {{ record.record_date }} - {{ t('type_label') }}: {{ record.type }} - {{ t('description_label') }}: {{ record.description || '無' }}
          </li>
        </ul>
      </div>

      <hr />

      <h3>{{ t('pet_behavior_logs') }}</h3>
      <form @submit.prevent="addBehaviorLog" class="form-section">
        <h4>{{ t('add_new_behavior_log') }}</h4>
        <div class="form-group">
          <label for="log-date">{{ t('log_date') }}：</label>
          <input type="date" id="log-date" v-model="newLogDate" required />
        </div>
        <div class="form-group">
          <label for="log-behavior">{{ t('behavior') }}：</label>
          <input type="text" id="log-behavior" v-model="newLogBehavior" placeholder="e.g., Active, Lethargic" />
        </div>
        <div class="form-group">
          <label for="log-emotion">{{ t('emotion') }}：</label>
          <input type="text" id="log-emotion" v-model="newLogEmotion" placeholder="e.g., Happy, Anxious" />
        </div>
        <div class="form-group">
          <label for="log-appetite">{{ t('appetite') }}：</label>
          <input type="text" id="log-appetite" v-model="newLogAppetite" placeholder="e.g., Normal, Reduced" />
        </div>
        <div class="form-group">
          <label for="log-notes">{{ t('notes') }}：</label>
          <textarea id="log-notes" v-model="newLogNotes"></textarea>
        </div>
        <button type="submit">{{ t('add_log_btn') }}</button>
      </form>

      <div class="list-section">
        <h4>{{ t('current_behavior_logs') }}</h4>
        <p v-if="petStore.behaviorLogs.length === 0">{{ t('no_behavior_logs') }}</p>
        <ul>
          <li v-for="log in petStore.behaviorLogs" :key="log.id">
            {{ t('date_label') }}: {{ log.log_date }} - {{ t('behavior') }}: {{ log.behavior || '無' }} - {{ t('emotion') }}: {{ log.emotion || '無' }} - {{ t('appetite') }}: {{ log.appetite || '無' }}
            <br />
            {{ t('notes') }}: {{ log.notes || '無' }}
          </li>
        </ul>
      </div>

    </div>
    <p v-else>{{ t('select_pet_to_view_add_records') }}</p>
  </div>
</template>

<style scoped>
.dashboard-container {
  max-width: 900px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.logout-btn {
  background-color: #f44336;
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}
.logout-btn:hover {
  background-color: #d32f2f;
}
hr {
  margin: 20px 0;
  border: 0;
  border-top: 1px solid #eee;
}
h3 {
  margin-top: 25px;
  margin-bottom: 15px;
}
h4 {
  margin-top: 15px;
  margin-bottom: 10px;
  color: #333;
}
.form-section, .list-section {
  padding: 15px;
  border: 1px solid #eee;
  border-radius: 5px;
  margin-bottom: 20px;
}
.form-group {
  margin-bottom: 10px;
}
label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
input[type="text"],
input[type="date"],
textarea,
select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box; /* Ensures padding doesn't expand the element */
}
textarea {
  min-height: 60px;
  resize: vertical;
}
button {
  padding: 8px 15px;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  margin-top: 10px;
}
button:hover {
  background-color: #369c71;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  background-color: #f9f9f9;
  border: 1px solid #eee;
  padding: 10px;
  margin-bottom: 5px;
  border-radius: 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
li.selected-pet {
    background-color: #e6ffe6; /* Light green for selected pet */
    border-color: #42b983;
    font-weight: bold;
}
.pet-actions button {
    margin-left: 10px;
    padding: 5px 10px;
    font-size: 12px;
}
.error-message {
  color: red;
  margin-top: 10px;
  text-align: center;
}
</style>
