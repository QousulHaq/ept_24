<template>
  <div>please wait...</div>
</template>

<script>
  import { parse } from 'querystring'
  import { mapActions } from 'vuex'

  export default {
    name: "AuthCallback",
    methods: {
      ...mapActions('auth', {
        login: 'login'
      })
    },
    created () {
      this.login(parse(this.$route.path.substr(1))).then(() => {
        this.$router.push({ name: 'exam.list' })
      })
    }
  }
</script>

<style scoped>

</style>
