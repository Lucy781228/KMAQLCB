<script>
  import { Bar, mixins } from 'vue-chartjs'
  
  export default {
    name: 'Bar',
    extends: Bar,
    mixins: [mixins.reactiveProp],
    props: ['chartData', 'options'],
    mounted () {
      this.renderChart(this.chartData, this.options);
    }
  }
  </script>