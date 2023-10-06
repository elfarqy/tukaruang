<template>
    <div>
      <h1>Currency Exchange</h1>
      <form @submit.prevent="submitExchange">
        <label for="currency">Select Currency:</label>
        <select id="currency" v-model="selectedCurrency" @change="resetFetchInterval">
          <option v-for="currency in currencies" :value="currency" :key="currency">{{ currency }}</option>
        </select>
        <label for="amount">Amount:</label>
        <input type="number" id="amount" v-model="amount" @input="calculateExchange" step="0.01">
        <label for="result">Result:</label>
        <input type="text" id="result" :value="exchangeResult.toFixed(2)" disabled>
        <button type="submit">Submit</button>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        selectedCurrency: 'USD',
        currencies: ['USD', 'EUR', 'GBP', 'JPY'], // Add more currencies as needed
        amount: 1,
        exchangeResult: 0,
        fetchInterval: null,
        currentPrice:0
      };
    },
    computed: {
    isAmountEmpty() {
      return !this.amount || isNaN(this.amount);
    },
  },
    mounted() {
      this.fetchExchangeRate();
      this.fetchInterval = setInterval(this.fetchExchangeRate, 15000); // Fetch data every 15 seconds
    },
    beforeUnmount() {
      clearInterval(this.fetchInterval);
    },
    methods: {
      async fetchExchangeRate() {
        try {
          const response = await axios.get('https://api.frankfurter.app/latest', {
            params:{
                from: this.selectedCurrency, 
                to: "IDR"
            }
          });
          if (response.status === 200) {
            const data = response.data;
            this.exchangeResult = data.rates.IDR * this.amount;
            this.currentPrice = data.rates.IDR
          } else {
            console.error('Failed to fetch data from the API');
          }
        } catch (error) {
          console.error('Fetch error:', error);
          this.exchangeResult = 0;
        }
      },
      resetFetchInterval() {
        this.fetchExchangeRate();
      },
      async submitExchange() {
        try {
          const formData = {
            currency_target: "IDR",
            amount: this.amount,
            currency_source: this.selectedCurrency,
            current_price: this.currentPrice
          };
          const token = localStorage.getItem('token');

          let config = {
            headers: {
                Authorization: "Bearer "+token,
                'Content-Type': 'application/json',
            }
        };
  
          const response = await axios.post('http://localhost:8081/backend/backend/web/api/sell', formData, config);
        //   console.log(response.message);
          if (response.status === 200) {
            this.$toast.success(response.data.message);
            this.amount = 0;
            this.exchangeResult = 0;
          } else {
            this.$toast.error(response.message);
          }
        } catch (error) {
            if (error.response) {
                this.$toast.error(`${error.response.data.message}`);
            } else if (error.request) {
                this.$toast.error('No response received from the server. Check your internet connection or server availability.');
            } else {
                this.$toast.error('An error occurred while processing the request.');
            }
        }
      },
        calculateExchange() {
            if (!this.isAmountEmpty) {
                this.fetchExchangeRate();
            } else {
                this.exchangeResult = 0;
            }
        },
    },
  };
  </script>
  
  <style scoped>
  /* Your component-specific styles */
  </style>
  