import { defineStore } from 'pinia';
import axios from 'axios';

export const usePetStore = defineStore('pet', {
  state: () => ({
    pets: [],
    selectedPetId: null,
    healthRecords: [],
    behaviorLogs: [],
  }),
  getters: {
    selectedPet: (state) => state.pets.find(pet => pet.id === state.selectedPetId),
  },
  actions: {
    async fetchPets() {
      try {
        const response = await axios.get('/pets');
        this.pets = response.data;
        if (this.pets.length > 0 && !this.selectedPetId) {
          this.selectedPetId = this.pets[0].id; // 預設選擇第一個寵物
          this.fetchRecordsAndLogs(this.selectedPetId);
        } else if (this.selectedPetId) {
          // 如果之前有選擇，確保它仍然在列表中
          if (!this.pets.some(pet => pet.id === this.selectedPetId)) {
            this.selectedPetId = this.pets.length > 0 ? this.pets[0].id : null;
            if (this.selectedPetId) {
                this.fetchRecordsAndLogs(this.selectedPetId);
            } else {
                this.healthRecords = [];
                this.behaviorLogs = [];
            }
          } else {
             this.fetchRecordsAndLogs(this.selectedPetId); // 重新載入當前寵物的記錄
          }
        }
      } catch (error) {
        console.error('Failed to fetch pets:', error);
        this.pets = [];
      }
    },
    async addPet(petData) {
      try {
        const response = await axios.post('/pets', petData);
        this.pets.push(response.data.pet);
        // 如果是第一個寵物，自動選中
        if (this.pets.length === 1) {
            this.selectedPetId = response.data.pet.id;
            this.fetchRecordsAndLogs(this.selectedPetId);
        }
        return response.data.message;
      } catch (error) {
        throw error;
      }
    },
    async fetchRecordsAndLogs(petId) {
      if (!petId) {
        this.healthRecords = [];
        this.behaviorLogs = [];
        return;
      }
      try {
        const healthResponse = await axios.get(`/pets/${petId}/health-records`);
        this.healthRecords = healthResponse.data;

        const behaviorResponse = await axios.get(`/pets/${petId}/behavior-logs`);
        this.behaviorLogs = behaviorResponse.data;
      } catch (error) {
        console.error('Failed to fetch records or logs:', error);
        this.healthRecords = [];
        this.behaviorLogs = [];
        throw error;
      }
    },
    async addHealthRecord(recordData) {
        try {
            const response = await axios.post('/health-records', recordData);
            this.healthRecords.push(response.data.record);
            return response.data.message;
        } catch (error) {
            throw error;
        }
    },
    async addBehaviorLog(logData) {
        try {
            const response = await axios.post('/behavior-logs', logData);
            this.behaviorLogs.push(response.data.log);
            return response.data.message;
        } catch (error) {
            throw error;
        }
    },
    setSelectedPet(petId) {
      this.selectedPetId = petId;
      this.fetchRecordsAndLogs(petId);
    },
  },
});
