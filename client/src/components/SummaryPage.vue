<!-- CurrencyData.vue -->
<template>
  <div>
    <h1>Currency Data</h1>
    <DateRangeFilter :onDateRangeChange="fetchCurrencyData" />
    <CurrencyTable :currencyDataList="filteredCurrencyData" />
  </div>
</template>

<script>
import axios from 'axios';
import DateRangeFilter from './DateRangeFilter.vue';
import CurrencyTable from './TableComponents.vue';

export default {
  name: 'CurrencyData',
  components: {
    DateRangeFilter,
    CurrencyTable,
  },
  data() {
    return {
      currencyDataList: [], // Data to populate the table
      filteredCurrencyData: [], // Data filtered by date range
    };
  },
  methods: {
    async fetchCurrencyData(dateRange) {

      const token = localStorage.getItem('token');
      try {
        // Make an Axios request to fetch currency data based on the selected date range
        const response = await axios.get(`http://localhost:8081/backend/backend/web/api/summary`, {
          params: {
            start_date: dateRange[0],
            end_date: dateRange[1],
          },
          headers: {
                Authorization: "Bearer "+token,
                'Content-Type': 'application/json',
          }
        });

        // Update the currencyDataList with the response data
        // Extract relevant data from the API response
        const currencyData = response.data.data.results;

        // Update the currencyDataList with the extracted data
        this.currencyDataList = currencyData;

        // Filter data based on date range (if needed)
        this.filteredCurrencyData = currencyData;
      } catch (error) {
        console.error('Error fetching currency data:', error);
      }
    },
  },
};
</script>
