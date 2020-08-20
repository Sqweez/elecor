<template>
    <v-dialog max-width="600" persistent v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Экспорт дебиторской задолженности</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-select
                    label="Выберите вариант экспорта"
                    :items="variants"
                    item-value="value"
                    item-text="name"
                    v-model="exportVariant"
                ></v-select>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="$emit('submit', exportVariant)">Экспорт <v-icon>mdi-check</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import moment from "moment";

    export default {
        data: () => ({
            exportVariant: null,
            variants: [],
            monthNames: [
                'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
            ],
        }),
        methods: {
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
        },
        computed: {},
        async created() {
            this.variants = this.parseMonths();
            this.exportVariant = this.variants[0].value;
        },
        props: {
            state: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<style scoped>

</style>
