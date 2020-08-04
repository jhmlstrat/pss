var GameComponent = {
  name: 'GameComponent',
  template: `
    <div class='container'>
{{ currentComponent() }}
      <b-row>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='#' v-on:click='switchToMenu();'>
            Pss Menu
          </b-button>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
      team: {"team_name":""},
      teamname: 't',
    }
  },
  watch: {
  },
  mounted() {
    eBus.$on('configUpdated',(c) => { this.config = vue.cyConfig;});
    eBus.$on('teamUpdated',(t) => { this.team = t;});
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
  },
};

