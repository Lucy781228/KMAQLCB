<template>
    <div class="main-container" v-if="loaded">
        <div class=line-item>
            <div class="icon-container">
                <div class="text-container">
                    <span>Số quá trình công tác</span>
                </div>
                <div class="icons">
                    <NcButton aria-label="Example text" type="tertiary">
                        <template #icon>
                            <ArrowCollapseDown :size="14" />
                        </template>
                    </NcButton>
                </div>
            </div>
            <div class="chart-container">
                <LineChart :chart-data="businessData.chartData" :options="businessData.chartOptions" ref="businessData"
                    :width="200" :height="570" />
            </div>
        </div>
        <div class=line-item>
            <div class="icon-container">
                <div class="text-container">
                    <span>Số quá trình đào tạo</span>
                </div>
                <div class="icons">
                    <NcButton aria-label="Example text" type="tertiary">
                        <template #icon>
                            <ArrowCollapseDown :size="14" />
                        </template>
                    </NcButton>
                </div>
            </div>
            <div class="chart-container">
                <LineChart :chart-data="educationData.chartData" :options="educationData.chartOptions"
                    ref="educationData" :width="200" :height="570" />
            </div>
        </div>
    </div>
</template>

<script>
import LineChart from './LineChart.vue'
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import FilterVariant from 'vue-material-design-icons/FilterVariant'
import ArrowCollapseDown from 'vue-material-design-icons/ArrowCollapseDown'
import { NcModal, NcButton } from "@nextcloud/vue";

export default {
    name: 'EducationBusinessChart',
    components: { NcButton, FilterVariant, ArrowCollapseDown, LineChart },
    props: {
        startDate: {
            type: String,
            required: true
        },

        endDate: {
            type: String,
            default: true
        }
    },
    data() {
        return {
            loaded: false,
            businessData: {
                chartData: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Số quá trình công tác',
                            borderColor: '#006AA3',
                            borderWidth: 1,
                            data: [],
                            lineTension: 0,
                            fill: false,
                            backgroundColor: 'transparent'
                        }
                    ]
                },
                chartOptions: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function (value) {
                                    if (Number.isInteger(value)) {
                                        return value;
                                    }
                                }
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        enabled: true,
                    }
                }
            },

            educationData: {
                chartData: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Số quá trình đào tạo',
                            borderColor: '#006AA3',
                            borderWidth: 1,
                            data: [],
                            lineTension: 0,
                            fill: false,
                            backgroundColor: 'transparent'
                        }
                    ]
                },
                chartOptions: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function (value) {
                                    if (Number.isInteger(value)) {
                                        return value;
                                    }
                                }
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        enabled: true,
                    }
                }
            },
        }
    },

    async mounted() {
        await this.fetchData()
    },

    watch: {
        startDate: 'fetchData',
        endDate: 'fetchData'
    },

    methods: {
        async fetchData() {
            this.loaded = false;
            try {
                await this.countEducationPerUnit()

                this.loaded = true;

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        async countEducationPerUnit() {
            try {
                const response = await axios.get(generateUrl('/apps/qlcb/analyst/count_education_per_unit'), {
                    params: {
                        startDate: this.startDate ? this.startDate : null,
                        endDate: this.endDate ? this.endDate : null
                    }
                });
                const data = response.data.data;
                console.log(data)
                const labels = data.map(item => item.unit_name);
                const count = data.map(item => item.count);
                const total = count.reduce((acc, val) => acc + val, 0);
                const averageCount = total / count.length;

                this.educationData.chartData.labels = labels;
                this.educationData.chartData.datasets[0].data = count;
                this.educationData.chartData.datasets.push({
                    label: 'Trung bình',
                    borderColor: '#FF0000',
                    borderWidth: 2,
                    data: new Array(count.length).fill(averageCount),
                    lineTension: 0,
                    fill: false,
                    backgroundColor: 'transparent'
                });

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        downloadBarChart() {
            const chart = this.$refs.barChart.$data._chart;
            if (chart) {
                const url = chart.toBase64Image();
                const link = document.createElement('a');
                link.href = url;
                link.download = 'chart.png';
                link.click();
            } else {
                console.error('Chart instance not found');
            }
        }
    }
}
</script>

<style scoped>
.container {
    overflow-x: auto;
    max-width: 528px;
    width: 528px;
}

.main-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-gap: 20px;
    padding-top: 20px;
    padding-bottom: 20px;
    width: 100%;
    height: 100%
}

.line-item {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    height: 100%;
}

.icon-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.text-container {
    font-size: 16px;
    font-weight: bold;
}

.icons {
    display: flex;
    align-items: center;
    visibility: hidden;
}

.line-item:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.line-item:hover .icons {
    visibility: visible;
}
</style>