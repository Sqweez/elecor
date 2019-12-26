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
                        v-model="login"
                        name="login"
                        prepend-icon="mdi-account"
                        type="text">
                    </v-text-field>
                    <v-text-field
                        @keypress.enter="doLogin"
                        label="Пароль"
                        name="password"
                        v-model="password"
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
    import showToast from "../../utils/Toast";

    export default {
        data: () => ({
            login: '',
            password: '',
        }),
        methods: {
            async doLogin() {
                const response = await this.$store.dispatch('login', {
                    login: this.login, password: this.password
                });
                if (response.error) {
                    showToast(response.error, 'Ошибка', 'error');
                    return;
                }

                await this.$router.push('/');
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "Login";
</style>
