<!-- src/views/Summary.vue -->
<template>
    <div>
      <h1>Summary</h1>
      <Table :items="tableData" />
    </div>
  </template>
  
  <script>
  import Table from '@/components/TableComponents.vue'; // Import the Table component
  
  export default {
    data() {
      return {
        tableData: [
          { id: 1, column1: 'Data 1', column2: 'Data A' },
          { id: 2, column1: 'Data 2', column2: 'Data B' },
          // Add more data objects as needed
        ],
      };
    },
    components: {
      Table, // Register the Table component
    },
  };
  </script>
  
  <style scoped>
  /* Your view-specific styles */
  </style>
  