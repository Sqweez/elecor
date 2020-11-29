<template>
    <v-dialog max-width="800" persistent v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Экспорт клиентов</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div class="checkbox-container">
                    <p>Тип клиентов:</p>
                    <v-checkbox
                        v-for="type of client_types"
                        :key="`client-type-${type.id}`"
                        :label="type.type"
                        :value="type.id"
                        v-model="client_type"
                        dense
                    />
                </div>
                <div class="checkbox-container">
                    <p>Пол:</p>
                    <v-radio-group
                        row
                        v-model="gender"
                    >
                        <v-radio
                            v-for="gender of genders"
                            :key="`gender-${gender.id}`"
                            :label="gender.gender"
                            :value="gender.id"
                        ></v-radio>
                    </v-radio-group>
                </div>
                <div class="checkbox-container">
                    <p>Язык:</p>
                    <v-radio-group
                        row
                        v-model="lang"
                    >
                        <v-radio
                            v-for="language of languages"
                            :key="`language-${language.id}`"
                            :label="language.lang"
                            :value="language.id"
                        ></v-radio>
                    </v-radio-group>
                </div>
                <v-select
                    class="my-2"
                    label="Услуги"
                    :items="services"
                    item-value="id"
                    item-text="name"
                    v-model="service"
                />
                <div v-if="isServiceChosen">
                    <div class="checkbox-container">
                        <p>Тариф:</p>
                        <v-checkbox
                            label="Любой"
                            v-model="everyPrice"
                        />
                        <v-text-field
                            type="number"
                            v-if="!everyPrice"
                            v-model="price"
                        />
                    </div>
                    <div class="checkbox-container">
                        <p>Активный клиент:</p>
                        <v-radio-group
                            row
                            v-model="active_client"
                        >
                            <v-radio
                                label="Все"
                                :value="-1"
                            ></v-radio>
                            <v-radio
                                label="Активный"
                                :value="1"
                            ></v-radio>
                            <v-radio
                                label="Неактивный"
                                :value="0"
                            ></v-radio>
                        </v-radio-group>
                    </div>
                    <div class="checkbox-container">
                        <p>Должник:</p>
                        <v-radio-group
                            row
                            v-model="debtor_client"
                        >
                            <v-radio
                                label="Все"
                                :value="-1"
                            ></v-radio>
                            <v-radio
                                label="Должник"
                                :value="1"
                            ></v-radio>
                            <v-radio
                                label="Не должник"
                                :value="0"
                            ></v-radio>
                        </v-radio-group>
                    </div>
                    <div class="checkbox-container">
                        <p>Договор заключен с:</p>
                        <v-select
                            class="my-2"
                            label="Компании"
                            :items="companies"
                            item-value="id"
                            item-text="name"
                            v-model="company"
                            multiple
                        >
                            <template v-slot:selection="{item, index}" v-if="allCompanySelected">
                        <span v-if="index === 0">
                           Все компании
                        </span>
                            </template>
                            <template v-slot:prepend-item>
                                <v-list-item
                                    ripple
                                    @click="selectAllCompanies"
                                >
                                    <v-list-item-action>
                                        <v-icon :color="company.length > 0 ? 'indigo darken-4' : ''">
                                            {{ icon }}
                                        </v-icon>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            Выбрать все
                                        </v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                                <v-divider class="mt-2"></v-divider>
                            </template>
                        </v-select>
                    </div>
                    <div class="checkbox-container">
                        <p>Период подключения:</p>
                        <div class="d-flex flex-column">
                            <div class="date-container">
                                <v-checkbox
                                    label="С начала работы программы"
                                    v-model="fromProgramStart"
                                />
                                <v-text-field
                                    v-if="!fromProgramStart"
                                    v-model="startDate"
                                    label="Дата начала"
                                    type="date"/>
                            </div>
                            <div class="date-container">
                                <v-checkbox
                                    label="По сегодняшний день"
                                    v-model="toToday"
                                />
                                <v-text-field
                                    v-if="!toToday"
                                    v-model="finishDate"
                                    label="Дата конца"
                                    type="date"/>
                            </div>
                        </div>
                    </div>
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="onSubmit">Экспорт
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import GETTERS from "../../../../store/getters";

    export default {
        data: () => ({
            // новый экспорт
            client_type: [],
            gender: -1,
            service: -1,
            lang: -1,
            everyPrice: true,
            price: 0,
            active_client: -1,
            debtor_client: -1,
            startDate: null,
            finishDate: null,
            fromProgramStart: false,
            toToday: false,
            company: [],
            today: new Date().toJSON().slice(0, 10),
            startOfUnixEpoch: new Date(0).toJSON().slice(0, 10)
        }),
        created() {
            this.startDate = this.today;
            this.finishDate = this.today;
        },
        methods: {
            onSubmit() {
                const params = this.createQuery();

                const urlParams = new URLSearchParams({
                    ...params
                }).toString()

                this.$emit('submit', urlParams);
            },
            createQuery() {
                const params = {
                    client_types: this.client_type,
                };

                if (this.gender !== -1) {
                    params.gender = this.gender;
                }

                if (this.lang !== -1) {
                    params.lang = this.lang;
                }

                if (this.isServiceChosen) {
                    params.service = this.service;

                    if (!this.everyPrice) {
                        params.price = this.price;
                    }

                    if (this.active_client !== -1) {
                        params.is_active = this.active_client;
                    }

                    if (this.debtor_client !== -1) {
                        params.is_debtor = this.debtor_client;
                    }

                    if (!this.allCompanySelected) {
                        params.company = this.company;
                    }

                    params.startDate = this.startDate;
                    params.finishDate = this.finishDate;
                }

                return params;
            },
            selectAllCompanies() {
                this.$nextTick(() => {
                    if (this.allCompanySelected) {
                        this.company = [];
                    } else {
                        this.company = this.companies.map(c => c.id).slice();
                    }
                })
            }
        },
        computed: {
            client_types() {
                const client_types = this.$store.getters[GETTERS.CLIENT_TYPES];
                this.client_type = client_types.map(c => c.id);
                return client_types;
            },
            genders() {
                return [{
                    id: -1,
                    gender: 'Все',
                }, ...this.$store.getters.GENDERS];
            },
            languages() {
                return [{
                    id: -1,
                    lang: 'Все',
                }, ...this.$store.getters.LANGUAGES];
            },
            services() {
                return [
                    {
                        id: -1,
                        name: 'Все'
                    }, ...this.$store.getters[GETTERS.SERVICES]];
            },
            companies() {
                const companies = this.$store.getters.COMPANIES;
                this.company = companies.map(c => c.id).slice();
                return companies;
            },
            isClientTypeFilled() {
                return this.client_type.length > 0;
            },
            isServiceChosen() {
                return this.service !== -1;
            },
            allCompanySelected() {
                return this.company.length === this.companies.length;
            },
            someCompanySelected() {
                return !this.allCompanySelected && this.company.length > 0;
            },
            icon() {
                if (this.allCompanySelected) return 'mdi-close-box'
                if (this.someCompanySelected) return 'mdi-minus-box'
                return 'mdi-checkbox-blank-outline'
            },
        },
        props: {
            state: {
                type: Boolean,
                default: false
            }
        },
        watch: {
            fromProgramStart(value) {
                if (value) {
                    this.startDate = this.startOfUnixEpoch;
                } else {
                    this.startDate = this.today;
                }
            },
            toToday(value) {
                this.finishDate = this.today;
            },
            state() {
                this.gender = -1;
                this.service = -1;
                this.lang = -1;
                this.everyPrice = true;
                this.price = 0;
                this.active_client = -1;
                this.debtor_client = -1;
                this.startDate = null;
                this.finishDate = null;
                this.fromProgramStart = false;
                this.toToday = false;
                this.today = new Date().toJSON().slice(0, 10);
                this.startOfUnixEpoch = new Date(0).toJSON().slice(0, 10);
            }
        }
    }
</script>

<style scoped>
    .checkbox-container {
        display: flex;
        align-items: center;
        column-gap: 8px;
    }

    .checkbox-container p {
        font-size: 16px;
        margin: 0 !important;
        font-weight: bold;
    }

    .date-container {
        display: flex;
        column-gap: 8px;
    }

    .date-container > div:first-child {
        width: 250px;
    }
</style>
