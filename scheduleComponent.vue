var ScheduleComponent = {
  name: 'ScheduleComponent',
  template: `
   <div class='container'>
{{ currentComponent() }}
     <div class='row'>
       <div class='col-xs-2 text-center'>
         <a role='button' class='btn btn-lg btn-link' href='#' v-on:click='switchToMenu();'>Pss Menu</a>
       </div>
       <div class='col-xs-8'>
       </div>
       <div class='col-xs-2 text-center'>
         <a role='button' class='btn btn-lg btn-link'href='/jhmlhome.html'>JHML Home</a>
       </div>
     </div>
{{ schedule }}
   </div>
  `,
  data() {
    return {
      teamname: 't',
      schedule: {},
    }
  },
  mounted() {
    eBus.$on('teamnameUpdated',(tn) => { this.teamname=tn; })
    eBus.$on('scheduleUpdated',(s) => { this.schedule=s; })
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  methods: {
    currentComponent() { return vue.currentComponent; },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
  },
};

