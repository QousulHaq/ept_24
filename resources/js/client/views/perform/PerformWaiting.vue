<template>
  <!--suppress HtmlDeprecatedAttribute -->
  <a-row type="flex" align="middle">
    <a-col :span="12">
      <h1><b>{{activeExam.name}}</b></h1>
      <h1>WAITING <b>PROCTOR</b> TO START AN EXAM</h1>
    </a-col>
    <a-col :span="12">
      <loading-icon/>
    </a-col>
  </a-row>
</template>

<script>
  import { mapGetters } from 'vuex'
  import LoadingIcon from '../../components/perform/LoadingIcon'

  export default {
    name: "PerformWaiting",
    components: { LoadingIcon },
    computed: {
      ...mapGetters('exam', ['isStarted', 'activeExam'])
    },
    watch: {
      'isStarted': function (newValue) {
        if (newValue)
          this.$router.push({ name: 'perform.tackle' })
      }
    }
  }
</script>

<style scoped>

</style>
