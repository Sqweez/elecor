<template>
    <v-dialog persistent max-width="700" v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Подключение разовой услуги</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onClose">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="pt-5">
                <v-form v-model="valid">
                    <div v-if="services.length">
                        <v-select
                            v-if="!newService"
                            @change="setPrice"
                            label="Разовая услуга"
                            :items="services"
                            :rules="[v => !!v || 'Выберите услугу']"
                            item-text="name"
                            item-value="id"
                            v-model="service_id"
                        ></v-select>
                        <v-checkbox v-model="newService" label="Услуга не из списка"/>
                    </div>
                    <v-text-field
                        v-model="name"
                        :rules="[v => !!v || 'Введите наименование услуги']"
                        v-if="!services.length || newService"
                        label="Разовая услуга"/>
                    <v-text-field
                        type="number"
                        label="Стоимость"
                        :rules="[v => !!v || 'Введите стоимость услуги']"
                        v-model="price"></v-text-field>
                </v-form>

                <v-divider></v-divider>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text @click="onClose">Отмена</v-btn>
                <v-btn text color="success" @click="saleService" :disabled="!valid">
                    Начислить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import GETTERS from "../../../store/getters";
    import ACTIONS from "../../../store/actions";
    export default {
        data: () => ({
            valid: true,
            newService: false,
            service_id: -1,
            name: '',
            price: null,
        }),
        computed: {
            getName() {
                return this.services.find(service => this.service_id === service.id).name;
            },
            services() {
                return this.$store.getters[GETTERS.TEMP_SERVICES](this.service);
            }
        },
        props: {
            service: {
                type: Number,
                default: 0,
            },
            connection: {
                type: Number,
                default: 0,
            },
            state: {
                type: Boolean,
                default: true,
            }
        },
        methods: {
            setPrice() {
                if (this.service_id === -1 || !this.services.length) {
                    this.price = null;
                }
                this.price = this.services.find(service => this.service_id === service.id).price;
            },
            onClose() {
                this.$emit('onClose');
            },
            async saleService() {
                const service = {
                    name: this.name || this.getName,
                    price: +this.price,
                    connection_id: this.connection,
                };
                await this.$store.dispatch(ACTIONS.SALE, service);
                this.$emit('onSale', {
                    name: service.name,
                    price: this.price,
                });
            }
        }

    }
</script>

<style scoped>

</style>
