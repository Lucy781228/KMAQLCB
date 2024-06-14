
 <script>
  import { Doughnut, mixins } from 'vue-chartjs'
  
  export default {
    name: 'Circle',
    extends: Doughnut,
    mixins: [mixins.reactiveProp],
    props: ['chartData', 'options'],
    mounted () {
      this.renderChart(this.chartData, this.options);
    }
  }
  </script>