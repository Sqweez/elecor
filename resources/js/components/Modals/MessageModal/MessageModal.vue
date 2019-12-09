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
                <v-text-field :label="messageTitle" v-model="title_"></v-text-field>
                <v-textarea :label="messageBody" rows="5" auto-grow v-model="body"></v-textarea>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text @click="onCancel">Отмена</v-btn>
                <v-btn text color="success" @click="onSend">Отправить</v-btn>
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
            },
            title: {
                type: String,
                default: 'Отправка сообщения'
            },
            messageTitle: {
                type: String,
                default: 'Заголовок сообщения'
            },
            messageBody: {
                type: String,
                default: 'Текст сообщения'
            }
        },
        data: () => ({
            title_: '',
            body: '',
        }),
        methods: {
            onCancel() {
                this.$emit('modalClosed');
                this.clearData();
            },
            onSend() {
                this.$emit('sendMessage', {title: this.title_, body: this.body});
                this.clearData();
            },
            clearData() {
                this.title_ = '';
                this.body = '';
            }
        }
    }
</script>

<style scoped>

</style>
