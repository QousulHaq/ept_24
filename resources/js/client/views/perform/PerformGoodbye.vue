<template>
  <!--suppress HtmlDeprecatedAttribute -->
  <a-row type="flex" align="middle">
    <a-col :span="12">
      <h1><b>{{activeExam.name}}</b></h1>
      <h1><b>CONGRATULATION</b> YOU'VE BEEN COMPLETING YOUR EXAM</h1>
      <a-button @click="$router.replace({ name: 'exam.list' })" type="primary" block>BACK TO HOMEPAGE</a-button>
    </a-col>
    <a-col :span="12">
      <quote-icon/>
    </a-col>
  </a-row></template>

<script>
  import { mapGetters } from 'vuex'
  import QuoteIcon from '../../components/perform/QuoteIcon'

  export default {
    name: "PerformGoodbye",
    computed: {
      ...mapGetters('exam', ['activeExam'])
    },
    components: { QuoteIcon }
  }
</script>

<style scoped>

</style>
