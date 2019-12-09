<template>
    <v-row class="login-container" justify="center" align="center">
        <v-card class="login" elevation="10">
            <v-toolbar
                color="primary"
                dark
                flat
            >
                <v-toolbar-title>Авторизация</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-form>
                    <v-text-field
                        label="Логин"
                        name="login"
                        prepend-icon="mdi-account"
                        type="text">
                    </v-text-field>
                    <v-text-field
                        label="Пароль"
                        name="password"
                        prepend-icon="mdi-lock"
                        type="password"
                    ></v-text-field>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer>
                </v-spacer>
                    <v-btn color="primary" block @click="doLogin">Войти</v-btn>
            </v-card-actions>
        </v-card>
    </v-row>
</template>

<script>
    export default {
        methods: {
            doLogin() {
                this.$router.push('/');
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "Login";
</style>
