<template>
    <v-dialog max-width="600" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">{{ title }}</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="onCancel">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-text-field label="Комментарий" v-model="comment" />
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text @click="onCancel">Отмена</v-btn>
                <v-btn text color="success" @click="onSave">Отправить</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: {
            state: {
                type: Boolean
            },
            title: {
                type: String,
                default: 'Комментарий к заявке'
            }
        },
        data: () => ({
            comment: ''
        }),
        methods: {
            onCancel() {
                this.$emit('onCancel');
                this.comment = '';
            },
            onSave() {
                this.$emit('onSave', {comment: this.comment});
                this.comment = '';
            }
        }
    }
</script>

<style scoped>

</style>
