<template>
    <v-dialog max-width="700" v-model="state" persistent>
        <v-card>
            <v-card-title class="blue darken-1 d-flex justify-content-between">
                <span class="white--text">{{ title }}</span>
                <v-btn icon text class="float-right" @click="closeModal">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div class="p-3" v-if="!loading">
                    <v-text-field label="Наименование услуги" v-model="service.name"/>
                    <v-text-field label="Тариф" type="number" v-model="service.price"/>
                    <v-text-field label="Торговое наименование по умолчанию" v-model="service.trademark_default"/>
                    <v-checkbox label="Разовая услуга" v-model="service.isOneTime" @change="select_key++"/>
                    <v-select
                        :key="select_key"
                        v-model="service.main_id"
                        :items="_services"
                        item-value="id"
                        v-show="!!service.isOneTime"
                        item-text="name"
                        label="Привязать к услуге"
                    />
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn text @click="closeModal">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="save">
                    Сохранить
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
    import ACTIONS from "../../../store/actions";

    export default {
        data: () => ({
            loading: false,
            select_key: null,
        }),
        props: {
            editMode: {
                type: Boolean,
                default: false,
            },
            services: {
                type: Array,
            },
            service: {
                type: Object,
                default: () => ({
                    name: '',
                    price: null,
                    trademark_default: '',
                    main_id: null,
                    isOneTime: false
                }),
            },
            state: {
                type: Boolean,
                default: false,
            },
            title: {
                type: String,
                default: 'Добавление услуги'
            },
        },
        computed: {
            _services() {
                return this.services.filter(s => s.id !== this.service.id);
            }
        },
        methods: {
            async save() {
                this.loading = true;

                if (!this.service.isOneTime) {
                    this.service.main_id = null;
                }

                delete this.service.isOneTime;

                if (!this.editMode) {
                    this.createService();
                } else {
                    this.editService();
                }

                this.loading = false;

                await this.$emit('onSave');
            },
            closeModal() {
                this.$emit('onClose');
            },
            async createService() {
                await this.$store.dispatch(ACTIONS.CREATE_SERVICE, this.service);
            },
            async editService() {
                await this.$store.dispatch(ACTIONS.EDIT_SERVICE, this.service);
            },
        }
    }
</script>

<style scoped>

</style>
