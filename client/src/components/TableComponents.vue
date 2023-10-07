<template>
  <div>
    <table>
      <thead>
        <tr>
          <th>Currency</th>
          <th>Total Sell</th>
          <th>Total Buy</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(currencyData, index) in currencyDataList" :key="index">
          <td>{{ currencyData.currency }}</td>
          <td>{{ currencyData.totalSell }}</td>
          <td>{{ currencyData.totalBuy }}</td>
          <td>{{ currencyData.amount }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'CurrencyTable',
  props: ['currencyDataList'],
};
</script>
