<template>
  <div>
    <div>
      <a-radio-group :value="time" @change="tagChange" size="large">
        <a-radio-button value="future">Upcoming</a-radio-button>
        <a-radio-button value="running">Running</a-radio-button>
        <a-radio-button value="past">History</a-radio-button>
      </a-radio-group>
    </div>
    <a-list :grid="{ gutter: 16, xs: 1, md: 2, xxl: 3 }"
            :loading="loading"
            :dataSource="exams"
            :style="{ marginTop: '30px' }">
      <a-list-item slot="renderItem" slot-scope="item">
        <a-card :title="item.name">
          <a-row>
            <a-col :span="12">
              <p>Scheduled at : {{dateFormat(item.scheduled_at)}}</p>
            </a-col>
          </a-row>
          <a-row type="flex" justify="space-between">
            <a-col><a-tag color="orange">{{item.package.code}}</a-tag></a-col>
            <a-col>
              <a-button v-if="readyToEnter(item)" type="primary" @click="toDetail(item.id)"> Detail<a-icon type="right" /> </a-button>
            </a-col>
          </a-row>
        </a-card>
      </a-list-item>
    </a-list>
  </div>
</template>

<script>
  import { mapActions, mapState } from 'vuex'
  import { formatRelative, differenceInSeconds } from 'date-fns'

  export default {
    name: "ExamList",
    data: () => ({
      exams: [],
    }),
    computed: {
      ...mapState('exam', {
        'loading': state => state.status === 'fetching',
        'time': state => state.params.state,
        'implant': state => state.matter.data
      })
    },
    watch: {
      'implant': function (newValue) {
        this.exams = newValue
      }
    },
    methods: {
      ...mapActions({
        fetchExams: 'exam/fetchExams',
        changeParams: 'exam/changeParams'
      }),
      readyToEnter (exam) {
        return differenceInSeconds(new Date(), new Date(exam.scheduled_at)) > 0
      },
      dateFormat (date) {
        return formatRelative(new Date(date), new Date())
      },
      tagChange (e) {
        this.changeParams({ state: e.target.value })
      },
      toDetail (id) {
        this.$router.push({name: 'exam.detail', params: { id }})
      }
    },
    mounted () {
      this.fetchExams()
    }
  }
</script>

<style scoped>

</style>
