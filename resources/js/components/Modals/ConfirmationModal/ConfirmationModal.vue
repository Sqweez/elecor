<template>
    <div>
        <v-dialog
            v-model="state"
            persistent
            width="500">
            <v-card>
                <v-card-title class="headline blue darken-1 justify-content-between">
                    <span class="white--text">{{ title }}</span>
                    <v-btn icon text class="float-right">
                        <v-icon color="white" @click="onCancel">
                            mdi-close
                        </v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text class="modal-text">
                    {{ message }}
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="onCancel">
                        {{ cancelMessage }}
                    </v-btn>
                    <v-btn color="success" text @click="onConfirm">
                        {{ okMessage }}
                        <v-icon>mdi-check</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
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
                default: 'Подтвердите действие'
            },
            message: {
                type: String,
                required: true
            },
            okMessage: {
                type: String,
                default: 'ОК'
            },
            cancelMessage: {
                type: String,
                default: 'Отмена'
            }
        },
        methods: {
            onConfirm() {
                this.$emit('confirm');
            },
            onCancel() {
                this.$emit('cancel');
            }
        }
    }
</script>

<style scoped>
 .modal-text {
     margin-top: 15px!important;;
     font-size: 16px;
 }
</style>
