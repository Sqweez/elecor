<template>
    <v-dialog persistent max-width="900" v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Пополнение баланса</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onClose">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="pt-5" v-if="!loading">
                <v-data-table
                    v-if="clients"
                    show-select
                    v-model="selectedClients"
                    item-key="key"
                    no-results-text="Нет результатов"
                    no-data-text="Нет данных"
                    :footer-props="{
                        'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                        'items-per-page-text': 'Записей на странице',
                    }"
                    :headers="headers"
                    :items="clients"
                >
                    <template v-slot:item.client="{item}" :style="{backgroundColor: (!item.client ? 'red' : 'transparent' ) }">
                        <td :style="{
                            backgroundColor: (!item.client ? 'tomato' : '#f5f5f5'),
                            color: (!item.client ? 'white' : 'black'),
                            width: '100%'
                            }">
                            <span v-if="!item.client">Контрагент не найден</span>
                            <span v-else>{{ item.client.name }}</span>
                        </td>
                    </template>
                    <template v-slot:item.account="{item}">
                        <v-text-field v-model="item.account" v-mask="'## ## ##'" @keypress.enter="findClient(item)"></v-text-field>
                    </template>
                    <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                        {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                    </template>
                </v-data-table>
                <v-form v-if="!clients">
                    <input type="file" class="d-none" ref="fileInputBalance" @change="uploadFile">
                    <v-btn block color="primary" @click="chooseFile">Загрузить файл
                        <v-icon>mdi-upload</v-icon>
                    </v-btn>
                </v-form>
                <v-divider></v-divider>
            </v-card-text>
            <v-card-actions v-if="!loading && clients">
                <v-spacer></v-spacer>
                <v-btn text @click="onClose">Отмена</v-btn>
                <v-btn text color="success" @click="addBalance">
                    Пополнить баланс
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
            <v-progress-linear
                indeterminate
                :active="loading"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
        </v-card>
    </v-dialog>
</template>

<script>
    import showToast from "../../../utils/Toast";
    import uploadFile, {deleteFile} from "../../../api/service/upload";
    import {parseBalance} from "../../../api/client/clientApi";
    import {addBalances, findAccount} from "../../../api/connection";

    export default {
        data: () => ({
            loading: false,
            fileUrl: null,
            clients: null,
            selectedClients: null,
            headers: [
                {
                    text: 'Контрагент',
                    value: 'client',
                    sortable: false,
                },
                {
                    text: 'Лицевой счет',
                    value: 'account',
                    sortable: false,
                },
                {
                    text: 'К зачислению',
                    value: 'balance',
                    sortable: false,
                }
            ]
        }),
        computed: {},
        props: {
            state: {
                type: Boolean,
                default: true,
            }
        },
        methods: {
            clearData() {
              this.clients = null;
              this.selectedClients = null;
              this.fileUrl = null;
            },
            onClose() {
                this.clearData();
                this.$emit('onClose')
            },
            async addBalance() {
                this.loading = true;
                await addBalances(this.selectedClients);
                this.$emit('onSave');
                this.clearData();
                this.loading = false;
            },
            async uploadFile(e) {
                const file = e.target.files[0];
                const condition = file.type === 'application/vnd.ms-excel' || file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                if (!condition) {
                    showToast('Ошибка!', 'Выбран неверный тип файла', 'error');
                    return;
                }
                this.loading = true;
                const filePath = await uploadFile(file);
                this.fileUrl = filePath.data;
                this.clients = await parseBalance(this.fileUrl);
                this.selectedClients = this.clients.filter(c => c.client !== null);
                this.loading = false;
                //await deleteFile(this.fileUrl);
                return;
            },
            chooseFile() {
                this.$refs.fileInputBalance.click();
            },
            async findClient(item) {
                const response = await findAccount(item.account);
                if (!response) {
                    showToast('Клиент с данным лицевым счетом не был найден!', '', 'error')
                    return;
                }
                this.clients = this.clients.map(c => {
                    if (item.key === c.key) {
                        c.client = response.client;
                    }
                    this.selectedClients.push(c);
                    return c;
                })
            }

        }

    }
</script>
