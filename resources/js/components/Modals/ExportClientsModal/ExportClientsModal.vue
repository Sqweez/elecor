<template>
    <v-dialog max-width="600" persistent v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Подключение новой услуги</span>
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
                    item-text="text"
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
    export default {
        data: () => ({
            exportVariant: 1,
            variants: [
                {
                    text: 'Экспорт всех клиентов',
                    value: 1
                },
                {
                    text: 'МТК все',
                    value: 2
                },
                {
                    text: 'МТК без долгов',
                    value: 3
                },
            ]
        }),
        methods: {},
        computed: {},
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
