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
                        v-model="dateStart"
                        item-value="value"
                        class="mr-5"
                        item-text="name"></v-select>
                    <v-select
                        label="Конец"
                        class="ml-5"
                        :items="months"
                        v-model="dateEnd"
                        item-value="value"
                        item-text="name"></v-select>
                </div>
                <v-btn color="primary" @click="getData" block>Отобразить статистику</v-btn>
            </v-card-text>
            <div v-if="dataCollection">
                <div class="chart-block">
                    <h2 class="text-center">Пополнений в кассе</h2>
                    <BarChart :chart-data="dataCollection.checkoutCash"/>
                </div>
                <div class="chart-block">
                    <h2 class="text-center">Денег в минусе</h2>
                    <BarChart :chart-data="dataCollection.debts"/>
                </div>
                <div class="chart-block">
                    <h2 class="text-center">Прирост абонентской платы</h2>
                    <BarChart :chart-data="dataCollection.increases"/>
                </div>
                <div class="chart-block">
                    <h2 class="text-center">Потери по абонентской плате</h2>
                    <BarChart :chart-data="dataCollection.decreases"/>
                </div>
            </div>

        </v-card>
    </div>
</template>

<script>
    import BarChart from "../Chart/BarChart";

    import moment from 'moment';
    import showToast from "../../../utils/Toast";
    import {getStats} from "../../../api/stats";

    export default {
        components: {
            BarChart,
        },
        mounted() {
            this.months = this.parseMonths();
        },
        data: () => ({
            dataCollection: null,
            dateStart: null,
            dateEnd: null,
            monthNames: [
                'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
            ],
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
            async getData() {
                if (!this.dateStart || !this.dateEnd) {
                    showToast('Выберите дату', '', 'warning');
                    return;
                }
                const dateStart = moment(this.dateStart).format('YYYY-MM-DD');
                const dateEnd = moment(this.dateEnd).endOf('month').format('YYYY-MM-DD');

                if(moment(dateEnd).diff(dateStart) < 1) {
                    showToast('Некорректный промежуток дат', '', 'warning');
                    return;
                }

                const response = await getStats({dateStart, dateEnd});

                this.dataCollection = {
                    checkoutCash: {
                        labels: [...response.checkoutCash.map(c => c.key)],
                        datasets: [{
                            label: 'Пополнений в кассе',
                            data: [
                                ...response.checkoutCash.map(c => c.sum)
                            ],
                            backgroundColor: Array(response.checkoutCash.length).fill('rgba(54, 162, 235, 0.2)'),
                            borderColor: Array(response.checkoutCash.length).fill('rgba(54, 162, 235, 1)'),
                            borderWidth: 1
                        }]
                    },
                    debts: {
                        labels: [...response.debts.map(c => c.key)],
                        datasets: [{
                            label: 'Денег в минусе',
                            data: [
                                ...response.debts.map(c => c.debt)
                            ],
                            backgroundColor: Array(response.debts.length).fill('rgba(54, 162, 235, 0.2)'),
                            borderColor: Array(response.debts.length).fill('rgba(54, 162, 235, 1)'),
                            borderWidth: 1
                        }]
                    },
                    increases: {
                        labels: [...response.increases.map(c => c.key)],
                        datasets: [{
                            label: 'Прирост абонентской платы',
                            data: [
                                ...response.increases.map(c => c.sum)
                            ],
                            backgroundColor: Array(response.increases.length).fill('rgba(54, 162, 235, 0.2)'),
                            borderColor: Array(response.increases.length).fill('rgba(54, 162, 235, 1)'),
                            borderWidth: 1
                        }]
                    },
                    decreases: {
                        labels: [...response.decreases.map(c => c.key)],
                        datasets: [{
                            label: 'Потери абонентской платы',
                            data: [
                                ...response.decreases.map(c => c.sum)
                            ],
                            backgroundColor: Array(response.decreases.length).fill('rgba(54, 162, 235, 0.2)'),
                            borderColor: Array(response.decreases.length).fill('rgba(54, 162, 235, 1)'),
                            borderWidth: 1
                        }]
                    }
                };


            },
            parseMonths() {
                let dateEnd = moment();
                let dateStart = moment().subtract(11, 'months');
                let interim = dateStart.clone();
                let timeValues = [];

                while (dateEnd > interim || interim.format('M') === dateEnd.format('M')) {
                    timeValues.push(interim.format('YYYY-MM'));
                    interim.add(1,'month');
                }

                return timeValues.map((m) => {
                    const dates = m.split('-');
                    const year = dates[0];
                    const month = this.monthNames[parseInt(dates[1]) - 1];
                    return {
                        name: `${month}, ${year} г.`,
                        value: m + '-01',
                    };
                }).reverse();
            },
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
            },
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
