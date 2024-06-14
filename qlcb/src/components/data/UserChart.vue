<template>
    <div class="main-container" v-if="loaded">
        <div class="circle">
            <div class="circle-item">
                <div class="icon-container">
                    <div class="text-container">
                        <span>Theo đơn vị</span>
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
                    <BarChart :chart-data="unitData.chartData" :options="unitData.chartOptions" ref="unitData" :width="300"
                        :height="180" />
                </div>
            </div>
            <div class="circle-item">
                <div class="icon-container">
                    <div class="text-container">
                        <span>Theo độ tuổi</span>
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
                    <CircleChart :chart-data="ageData.chartData" :options="ageData.chartOptions" ref="line" :width="200"
                        :height="180" />
                </div>
            </div>
            <div class="circle-item">
                <div class="icon-container">
                    <div class="text-container">
                        <span>Theo giới tính</span>
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
                    <CircleChart :chart-data="genderData.chartData" :options="genderData.chartOptions" ref="line" :width="200"
                        :height="180" />
                </div>
            </div>
        </div>
        <div class=line-item v-if="userLoaded">
            <div class="icon-container">
                <div class="text-container">
                        <span>Tổng số lượng cán bộ</span>
                    </div>
                <div class="icons">
                    <NcButton aria-label="Example text" type="tertiary">
                        <template #icon>
                            <ArrowCollapseDown :size="14" />
                        </template>
                    </NcButton>
                    <NcActions>
                        <NcActionRadio :checked="interval === 'week'" value="week" name="interval"
                            @change="handleIntervalChange">Tuần
                        </NcActionRadio>
                        <NcActionRadio :checked="interval === 'month'" value="month" name="interval"
                            @change="handleIntervalChange">Tháng
                        </NcActionRadio>
                        <NcActionRadio :checked="interval === 'quarter'" value="quarter" name="interval"
                            @change="handleIntervalChange">Quý
                        </NcActionRadio>
                        <NcActionRadio :checked="interval === 'year'" value="year" name="interval"
                            @change="handleIntervalChange">Năm
                        </NcActionRadio>
                    </NcActions>
                </div>
            </div>
            <div class="chart-container">
                <LineChart :chart-data="userData.chartData" :options="userData.chartOptions" ref="userData" :width="300"
                    :height="300" />
            </div>
        </div>
    </div>
</template>

<script>
import LineChart from './LineChart.vue'
import BarChart from './BarChart.vue'
import CircleChart from './CircleChart.vue'
import { getCurrentUser } from '@nextcloud/auth'
import axios from "@nextcloud/axios";
import { generateUrl } from '@nextcloud/router'
import FilterVariant from 'vue-material-design-icons/FilterVariant'
import ArrowCollapseDown from 'vue-material-design-icons/ArrowCollapseDown'
import { NcModal, NcActionRadio, NcButton, NcActions, NcActionButton } from "@nextcloud/vue";

export default {
    name: 'UserChart',
    components: {
        LineChart,
        NcButton,
        FilterVariant,
        ArrowCollapseDown,
        NcActions,
        NcActionButton,
        NcActionRadio,
        CircleChart,
        BarChart
    },
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
            interval: 'quarter',
            loaded: false,
            userLoaded: false,
            userData: {
                chartData: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Số lượng cán bộ',
                            backgroundColor: 'rgba(0, 106, 163, 0.2)', // Màu nền nhạt hơn của #006AA3
                            borderColor: '#006AA3', // Màu viền là #006AA3
                            borderWidth: 1,
                            data: [],
                            lineTension: 0
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
                                callback: function(value) {
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

            ageData: {
                chartData: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Số lượng cán bộ',
                            backgroundColor: ['#006AA3', '#3399CC', '#66B2E0', '#99D6EA'],
                            data: []
                        }
                    ]
                },
                chartOptions: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            },

            genderData: {
                chartData: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Số lượng cán bộ',
                            backgroundColor: ['#006AA3', '#66B2E0'],
                            data: []
                        }
                    ]
                },
                chartOptions: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            },

            unitData: {
                chartData: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Số lượng cán bộ',
                            backgroundColor: '#66B2E0',
                            data: []
                        },
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
                                callback: function(value) {
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
                    },
                }
            },
        }
    },
    async mounted() {
        await this.fetchData();
    },
    watch: {
        startDate: 'fetchData',
        endDate: 'fetchData'
    },
    methods: {
        async fetchData() {
            this.loaded = false;
            try {
                await this.countUsersByTime()
                await this.countUsersByUnit()
                await this.countUsersByAge()
                await this.countUsersByGender()
                this.loaded = true;

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        async countUsersByTime() {
            this.userLoaded = false;
            try {
                const response = await axios.get(generateUrl('/apps/qlcb/analyst/count_users_by_time'), {
                    params: {
                        startDate: this.startDate ? this.startDate : null,
                        endDate: this.endDate ? this.endDate : null,
                        interval: this.interval
                    }
                });
                const data = response.data.data;
                const labels = data.map(item => item.period);
                const userCount = data.map(item => item.user_count);

                this.userData.chartData.labels = labels;
                this.userData.chartData.datasets[0].data = userCount;
                this.userLoaded = true;

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        async countUsersByUnit() {
            try {
                const response = await axios.get(generateUrl('/apps/qlcb/analyst/count_users_by_unit'), {
                    params: {
                        startDate: this.startDate ? this.startDate : null,
                        endDate: this.endDate ? this.endDate : null
                    }
                });
                const data = response.data.data;
                const labels = data.map(item => item.unit_name);
                const userCount = data.map(item => item.user_count);

                this.unitData.chartData.labels = labels;
                this.unitData.chartData.datasets[0].data = userCount;

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        async countUsersByGender() {
            try {
                const response = await axios.get(generateUrl('/apps/qlcb/analyst/count_users_by_gender'), {
                    params: {
                        startDate: this.startDate ? this.startDate : null,
                        endDate: this.endDate ? this.endDate : null
                    }
                });
                const data = response.data.data;
                const labels = data.map(item => item.gender);
                const userCount = data.map(item => item.user_count);

                this.genderData.chartData.labels = labels;
                this.genderData.chartData.datasets[0].data = userCount;

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        async countUsersByAge() {
            try {
                const response = await axios.get(generateUrl('/apps/qlcb/analyst/count_users_by_age'), {
                    params: {
                        startDate: this.startDate ? this.startDate : null,
                        endDate: this.endDate ? this.endDate : null
                    }
                });
                const data = response.data.data;
                const labels = data.map(item => item.age_group);
                const userCount = data.map(item => item.user_count);

                this.ageData.chartData.labels = labels;
                this.ageData.chartData.datasets[0].data = userCount;

            } catch (e) {
                console.error('Error fetching data:', e);
            }
        },

        handleIntervalChange(event) {
            this.interval = event.target.value;
            this.countUsersByTime();
        }
    }
}
</script>

<style scoped>
.main-container {
    display: flex;
    flex-direction: column;
    height: 300px;
    padding-top: 20px;
    padding-bottom: 20px;
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

.circle {
    display: grid;
    grid-template-columns: 3fr 1fr 1fr;
    gap: 20px;
    width: 100%;
}

.circle-item,
.line-item {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    position: relative;
}

.circle-item:hover,
.line-item:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.circle-item:hover .icons,
.line-item:hover .icons {
    visibility: visible;
}

.line-item {
    margin: 20px 0;
}

.line-item .chart-container {
    margin-top: 10px;
}
</style>