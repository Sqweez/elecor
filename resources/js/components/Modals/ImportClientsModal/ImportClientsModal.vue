<template>
    <v-dialog max-width="1100" persistent v-model="state">
        <v-card>
            <v-card-title
                class="d-flex justify-content-between blue darken-1">
                <span class="white--text">Импорт клиентов</span>
                <v-btn icon text class="float-right" @click="closeModal">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div v-if="duplicates.length && !loading">
                    <h3>
                        Обнаружено дубликатов: {{ duplicates.length }}
                    </h3>
                    <v-data-table
                        show-select
                        v-model="selectedDuplicates"
                        item-key="key"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :footer-props="{
                        'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                        'items-per-page-text': 'Записей на странице',
                    }"
                        :headers="duplicateHeaders"
                        :items="duplicates"
                    >
                        <template v-slot:item.duplicates="{item}">
                            <div v-for="d of item.duplicates" :key="d.key">
                                <span>{{ d.name }}</span>
                                <v-btn icon @click="showProfile(d)">
                                    <v-icon>mdi-eye</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </div>
                <div v-if="parsedClients.length && !loading && !duplicates.length" class="p-3"
                >
                    <h3>
                        Выбрано клиентов: {{ selectedClients.length }}
                    </h3>
                    <v-data-table
                        v-if="parsedClients.length"
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
                        :items="parsedClients"
                    >
                        <template v-slot:item.name="{item}">
                            <v-text-field v-model="item.name"></v-text-field>
                        </template>
                        <template v-slot:item.phone="{item}">
                            <v-text-field v-model="item.phone" v-mask="'###########'"></v-text-field>
                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </div>
                <v-form class="p-3" v-if="!loading && !parsedClients.length">
                    <input type="file" class="d-none" ref="fileInput" @change="uploadFile">
                    <v-btn block color="primary" @click="chooseFile">Загрузить файл
                        <v-icon>mdi-upload</v-icon>
                    </v-btn>
                </v-form>
                <v-divider/>
            </v-card-text>
            <v-card-actions v-if="!loading">
                <v-btn text @click="closeModal">Отмена</v-btn>
                <v-spacer/>
                <v-btn text color="success" @click="checkDuplicates" v-if="parsedClients.length && !duplicates.length">
                    Загрузить клиентов
                    <v-icon>mdi-check</v-icon>
                </v-btn>
                <v-btn text color="success" @click="storeClients" v-if="duplicates.length">
                    Загрузить клиентов
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
    import {createParsedClients, parseClients} from "../../../api/client/clientApi";
    import ACTIONS from "../../../store/actions";
    import GETTERS from "../../../store/getters";

    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            }
        },
        computed: {
            currentProgress() {
                return (this.currentIndex / this.creatingTotal) * 100;
            }
        },
        data: () => ({
            duplicates: [],
            title: '',
            currentIndex: null,
            loading: false,
            parsedClients: [],
            creatingStarted: false,
            selectedClients: null,
            creatingTotal: null,
            fileUrl: '',
            headers: [
                {
                    text: '#',
                    value: 'key',
                    align: 'left'
                },
                {
                    text: 'Контрагент',
                    value: 'name',
                    align: 'center'
                },
                {
                    text: 'Телефон',
                    value: 'phone',
                    align: 'center'
                },
            ],
            selectedDuplicates: [],
            duplicateHeaders: [
                {
                    text: '#',
                    value: 'key',
                    align: 'left'
                },
                {
                    text: 'Импортируемый клиент',
                    value: 'importName',
                    align: 'center'
                },
                {
                    text: 'Дубликаты в базе',
                    value: 'duplicates',
                    align: 'center'
                },
            ]

        }),
        methods: {
            closeModal() {
                if (!this.loading) {
                    this.clearData();
                    this.$emit('onClose');
                }
            },
            showProfile(client) {
                const protocol = window.location.protocol;
                const hostName = window.location.host;
                const uri = `clients/${client.id}`;
                const url = `${protocol}//${hostName}/${uri}`;
                window.open(url, '_blank');
            },
            clearData() {
                this.parsedClients = [];
                this.selectedClients = null;
                this.fileUrl = '';
                this.duplicates = [];
                this.selectedDuplicates = [];
            },
            storeClients() {
                this.loading = true;
                const unusedClients = this.duplicates
                    .filter(d => {
                        return !this.selectedDuplicates.includes(d);
                    })
                    .map(d => {
                        return d.importName;
                    });
                this.selectedClients = this.selectedClients.filter(c => {
                    return !unusedClients.includes(c.name)
                });
                this.onSave();
                return;
            },
            async checkDuplicates() {
                this.loading = true;
                const duplicates = await this.$store.getters[GETTERS.CLIENT_DUPLICATES](this.selectedClients);
                if (!duplicates.length) {
                    this.onSave();
                    return;
                }
                this.loading = false;
                this.duplicates = duplicates.map((d, index) => {
                    d.key = index + 1;
                    return d;
                });
                this.selectedDuplicates = [...this.duplicates];
            },
            async onSave() {
                let clients = this.selectedClients.map(c => {
                    delete c.key;
                    c.phones = [];
                    if (c.phone) {
                        c.phones.push(c.phone);
                    }
                    return c;
                });

                this.loading = true;

                await createParsedClients(clients);

                await this.$store.dispatch(ACTIONS.GET_CLIENTS);

                this.loading = false;

                this.$emit('onSave');

                this.clearData();

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
                const parsedClients = await parseClients(this.fileUrl);
                await deleteFile(this.fileUrl);
                this.loading = false;
                if (parsedClients.length === 0) {
                    showToast('Ошибка!', 'В данном файле не обнаружены клиенты!', 'error');
                    return;
                }
                this.parsedClients = parsedClients.map((c, index) => {
                    c.key = ++index;
                    return c;
                });
                this.selectedClients = [...this.parsedClients];
            },
            chooseFile() {
                this.$refs.fileInput.click();
            }
        }
    }
</script>
