<!-- DateRangeFilter.vue -->
<template>
    <div>
      <vue-datepicker v-model="dateRange" type="date" format="yyyy-MM-dd" range />
    </div>
  </template>
  
  <script> 
  import '@vuepic/vue-datepicker/dist/main.css'; // Import the CSS
  import { ref } from 'vue';
  import VueDatepicker from '@vuepic/vue-datepicker';
  
  export default {
    name: 'DateRangeFilter',
    props: ['onDateRangeChange'],
    components: {
      VueDatepicker,
    },
    setup() {
      const dateRange = ref([]);
  
      return {
        dateRange,
      };
    },
    watch: {
      dateRange: {
        handler(newValue) {
          this.onDateRangeChange(newValue);
        },
        deep: true,
      },
    },
  };
  </script>
  