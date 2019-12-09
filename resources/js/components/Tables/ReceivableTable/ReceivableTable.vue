<template>
    <div>
        <v-card>
            <v-card-title>
                Дебиторская задолженность
            </v-card-title>
            <v-card-text class="p-3">
                <h3>Общая задолженность: {{totalDebt}} тенге</h3>
                <v-divider class="mt-4"></v-divider>
                <v-btn color="primary mt-4">Экспорт данных
                    <v-icon>mdi-file-excel-box</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-text-field
                    class="p-3"
                    v-model="search"
                    clearable
                    append-icon="mdi-account-search"
                    label="Поиск"
                    single-line
                    hide-details
                ></v-text-field>
                <v-data-table
                    @click:row="rowClick"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :footer-props="{
                        'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                        'items-per-page-text': 'Записей на странице',
                    }"
                    :headers="headers"
                    :items="clients"
                    :search="search"
                >
                    <template v-slot:item.personalAccount="{ item }">
                        <ul>
                            <li v-for="(i, index) of item.personalAccount" :key="index">{{ i }}</li>
                        </ul>
                    </template>
                    <template v-slot:item.address="{ item }">
                        <ul>
                            <li v-for="(i, index) of item.address" :key="index">{{ i }}</li>
                        </ul>
                    </template>
                    <template v-slot:item.trademark="{ item }">
                        <ul>
                            <li v-for="(i, index) of item.trademark" :key="index">{{ i }}</li>
                        </ul>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import getRandomArbitrary from "../../../utils/Random";

    export default {
        computed: {
            totalDebt() {
                return this.clients.reduce((a, c) => {
                    return a + parseFloat(c.debt);
                }, 0)
            },
            clients() {
                const clients = this.$store.getters.allClients;
                return clients.map(client => {
                    client.debt = `${Math.floor(getRandomArbitrary(1000, 10000))} тенге`;
                    return client;
                })
            },
        },
        data: () => ({
            search: '',
            headers: [
                {
                    text: 'Контрагент',
                    align: 'left',
                    sortable: false,
                    value: 'fullName',
                },
                {text: 'Лицевой счет', value: 'personalAccount', sortable: false},
                {text: 'Адрес', value: 'address', sortable: false},
                {text: 'Торговое наименование', value: 'trademark', sortable: false},
                {text: 'Задолженность', value: 'debt', sortable: false}
            ],
        }),
        methods: {
            rowClick(e) {
                this.$router.push({name: 'clients.show', params: {userId: e._id}})
            }
        }
    }
</script>

<style scoped>

</style>
