<template>
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-md">
          <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Scheduled</th>
            <th>Ended</th>
            <th>Started</th>
<!--            <th>Duration</th>-->
            <th v-if="state !== undefined && state !== ''">Action</th>
          </tr>
          </thead>
          <tbody>
          <tr v-if="items.length === 0">
            <td colspan="6">No Entries</td>
          </tr>
          <tr v-for="(item, key) in items" :key="key">
            <td>{{ key + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>{{ dashIfNull(dateFormat(item.scheduled_at)) }}</td>
            <td>{{ dashIfNull(dateFormat(item.ended_at)) }}</td>
            <td>{{ (item.started_at !== null) ? dateFormat(item.started_at) : 'Not Yet' }}</td>
<!--            <td>{{ item.duration }} Minute</td>-->
            <td v-if="state !== undefined && state !== ''">
              <template v-if="state === 'present'">
                <button v-if="!item.started_at" class="btn btn-outline-danger btn-sm" @click.prevent="startAnExam(item.id)">Start Exam</button>
                <button class="btn btn-danger btn-sm" v-else disabled>Exam Started</button>
              </template>
              <button class="btn btn-primary btn-sm" @click.prevent="redirect(getDetailUrl(item.id))">Detail</button>
              <button v-if="state === 'future'" class="btn btn-outline-warning btn-sm" @click.prevent="redirect(getEditUrl(item.id))">Edit</button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "ListExam",
    props: {
      state: {
        type: String
      },
      detailUrl: {
        type: String,
        required: true,
      },
      editUrl: {
        type: String,
        default: '',
      }
    },
    data () {
      return {
        items: []
      }
    },
    created () {
      this.getExams()
    },
    methods: {
      getExams () {
        this.request('api.back-office.exam.index', {}, {
          params: { 'state': this.state }
        })
          .then(response => {
            this.items = response.data.data
          })
      },
      startAnExam (exam) {
        this.request('api.back-office.exam.start-exam',
          { 'exam': exam }).then(() => {
            this.swal('Success', 'Exam was started!', 'success').finally(() => {
              this.getListen(exam)
              this.getExams()
            })
        })
      },
      getDetailUrl (id) {
        return this.detailUrl.replace(':id', id)
      },
      getEditUrl (id) {
        return this.editUrl.replace(':id', id)
      }
    }
  }
</script>

<style scoped>

</style>
