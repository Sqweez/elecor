<template>
    <div>
        <v-card>
            <v-card-title>
                Настройки реферральной системы
            </v-card-title>
            <v-card-text>
                <div class="p-3">
                    <div class="d-flex justify-content-end mb-3">
                    </div>
                    <v-simple-table>
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Шаблон сообщения</th>
                                <th>Базовая ссылка</th>
                                <th>Скидка</th>
                                <th>Подарочные бонусы</th>
                                <th>Шифровать ссылку?</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <v-text-field
                                        type="text"
                                        v-model="settings.message_template"
                                    />
                                </td>
                                <td>
                                    <v-text-field type="text" v-model="settings.base_url"/>
                                </td>
                                <td>
                                    <v-text-field type="number" v-model="settings.discount" />
                                </td>
                                <td>
                                    <v-text-field type="number" v-model="settings.cashback" />
                                </td>
                                <td class="d-flex justify-content-center">
                                    <v-checkbox
                                        v-model="settings.hash_ref"
                                    />
                                </td>
                            </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                    <div class="d-flex justify-content-end">
                        <v-btn color="success" class="my-5 text-left" @click="updateSettings">
                            Сохранить <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    export default {
        data: () => ({
            settings: {},
        }),
        methods: {
            async updateSettings() {
                await this.$store.dispatch('updateReferralSettings', this.settings);
            }
        },
        computed: {
            REFERRAL_SETTING() {
                return this.$store.getters.REFERRAL_SETTINGS;
            }
        },
        watch: {
            REFERRAL_SETTING(value) {
                this.settings = JSON.parse(JSON.stringify(value));
            }
        },
        async created() {
            await this.$store.dispatch('getReferralSettings');
        }
    }
</script>

<style scoped>

</style>
