<template>
    <v-dialog max-width="800" persistent v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Экспорт дебиторской задолженности v2</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-select
                    class="my-2"
                    label="Услуги"
                    :items="services"
                    item-value="id"
                    item-text="name"
                    v-model="selectedServices"
                    multiple
                >
                    <template v-slot:selection="{item, index}" v-if="allServicesSelected">
                        <span v-if="index === 0">
                            Все услуги
                        </span>
                    </template>
                    <template v-slot:prepend-item>
                        <v-list-item
                            ripple
                            @click="selectAllServices"
                        >
                            <v-list-item-action>
                                <v-icon :color="selectedServices.length > 0 ? 'indigo darken-4' : ''">
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
                <div class="date-container">
                    <v-checkbox
                        v-model="fromProgramStart"
                        label="С начала работы программы"
                    />
                    <v-text-field
                        v-if="!fromProgramStart"
                        v-model="startDate"
                        label="Дата начала"
                        type="date"/>
                </div>
                <div class="date-container">
                    <v-checkbox
                        v-model="toToday"
                        label="По сегодняшний день"
                    />
                    <v-text-field
                        v-if="!toToday"
                        v-model="finishDate"
                        label="Дата начала"
                        type="date"/>
                </div>
                <p class="attention attention--error" v-if="!allFieldsFilled">
                    Для экспорта дебиторской задолженности заполните все поля!
                </p>
                <p class="attention attention--success" v-if="allFieldsFilled">
                    Все поля заполнены!
                </p>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="success"
                    :disabled="!allFieldsFilled"
                    @click="onSubmit">
                    Экспорт
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import moment from "moment";
    import GETTERS from "../../../../store/getters";

    export default {
        data: () => ({
            selectedServices: [],
            fromProgramStart: false,
            toToday: false,
            startDate: null,
            finishDate: null
        }),
        mounted() {
            this.selectedServices.push(...this.services.map(c => c.id));
        },
        methods: {
            selectAllServices() {
                this.$nextTick(() => {
                    if (this.allServicesSelected) {
                        this.selectedServices = [];
                    } else {
                        this.selectedServices = this.services.map(c => c.id).slice();
                    }
                })
            },
            onSubmit() {
                const paramsObject = {
                    services: this.selectedServices,
                    startDate: this.startDate,
                    finishDate: this.finishDate
                };

                if (this.allServicesSelected) {
                    delete paramsObject.services;
                }

                const params = new URLSearchParams({
                    ...paramsObject
                }).toString();

                this.$emit('submit', params);
            }
        },
        watch: {
            state() {
                this.selectedServices = [];
                this.fromProgramStart = false;
                this.toToday = false;
                this.startDate = null;
                this.finishDate = null;
            },
            fromProgramStart(value) {
                if (value) {
                    this.startDate = new Date(0).toJSON().slice(0, 10);
                } else {
                    this.startDate = null;
                }
            },
            toToday(value) {
                if (value) {
                    this.finishDate = new Date().toJSON().slice(0, 10);
                } else {
                    this.finishDate = null;
                }
            }
        },
        computed: {
            services() {
                return this.$store.getters[GETTERS.SERVICES];
            },
            allServicesSelected() {
                return this.selectedServices.length === this.services.length;
            },
            someServicesSelected() {
                return this.selectedServices.length > 0 && !this.allServicesSelected;
            },
            icon() {
                if (this.allServicesSelected) return 'mdi-close-box'
                if (this.someServicesSelected) return 'mdi-minus-box'
                return 'mdi-checkbox-blank-outline'
            },
            allFieldsFilled() {
                return !!(this.selectedServices.length && this.startDate && this.finishDate);
            }
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
    .date-container {
        display: flex;
        column-gap: 8px;
    }

    .date-container > div:first-child {
        width: 250px;
    }

    .attention {
        text-align: center;
        font-size: 18px;
    }

    .attention--error {
        color: red;
    }

    .attention--success {
        color: green;
    }
</style>
