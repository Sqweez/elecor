<template>
    <div>
        <v-card>
            <v-card-title>
                Экономическая статистика
            </v-card-title>
            <v-card-text>
                <div class="d-flex">
                    <v-select
                        label="Начало"
                        :items="months"
                        class="mr-5"
                        item-text="name"></v-select>
                    <v-select
                        label="Конец"
                        class="ml-5"
                        :items="months"
                        item-text="name"></v-select>
                </div>
                <v-btn color="primary" block>Отобразить статистику</v-btn>
            </v-card-text>
            <div class="chart-block">
                <h2 class="text-center">Пополнений в кассе</h2>
                <BarChart :chart-data="dataCollection"/>
            </div>
            <div class="chart-block">
                <h2 class="text-center">Денег в минусе</h2>
                <BarChart :chart-data="dataCollection"/>
            </div>
            <div class="chart-block">
                <h2 class="text-center">Прирост абонентской платы</h2>
                <BarChart :chart-data="dataCollection"/>
            </div>
            <div class="chart-block">
                <h2 class="text-center">Потери по абонентской плате</h2>
                <BarChart :chart-data="dataCollection"/>
            </div>
        </v-card>
    </div>
</template>

<script>
    import BarChart from "../Chart/BarChart";

    export default {
        components: {
            BarChart,
        },
        mounted() {
            this.fillData();
        },
        data: () => ({
            dataCollection: null,
            months: [
                {name: 'Текущий месяц'},
                {name: 'Сентябрь, 2019'},
                {name: 'Август, 2019'},
                {name: 'Июль, 2019'},
                {name: 'Июнь, 2019'},
                {name: 'Май, 2019'},
                {name: 'Апрель, 2019'},
            ]
        }),
        methods: {
            fillData() {
                this.dataCollection = {
                    labels: ['Июль, 2019', 'Август, 2019', 'Сентябрь, 2019', 'Октябрь, 2019'],
                    datasets: [{
                        label: 'Экономические показатели',
                        data: [
                            this.getRandomInt(),
                            this.getRandomInt(),
                            this.getRandomInt(),
                            this.getRandomInt(),
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1
                    }]
                }
            },
            getRandomInt() {
                return Math.floor(Math.random() * (50 - 5 + 1)) + 5
            }
        }
    }
</script>

<style scoped>
    .chart-block {
        border: 1px solid #ccc;
        padding: 20px;
        max-width: 800px;
        margin: auto;
    }
</style>
