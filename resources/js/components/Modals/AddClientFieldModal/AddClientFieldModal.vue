<template>
    <v-dialog max-width="600" persistent v-model="state">
        <v-card>
            <v-card-title
                class="d-flex justify-content-between blue darken-1">
                <span class="white--text">Добавление поля</span>
                <v-btn icon text class="float-right" @click="$emit('onClose')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-3">
                    <v-text-field label="Название поля" v-model="title"/>
                </v-form>
                <v-divider/>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="$emit('onClose')">Отмена</v-btn>
                <v-spacer />
                <v-btn text color="success" @click="onSave">Сохранить <v-icon>mdi-check</v-icon></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            }
        },
        data: () => ({
            title: ''
        }),
        methods: {
            onSave() {
                this.$emit('onClose')
            }
        }
    }
</script>

<style scoped>

</style>
