<template>
  <div>
    <highcharts :options="chartOptions"></highcharts>
  </div>
</template>

<script>
  export default {
    props: ['tree'],
    data() {
      return {
        chartOptions: {
          chart: {
            //height: '100%',
            height: 600,
            inverted: true
          },
          title: {
            useHTML: true,
            text: this.trans.get('keys.so_do_cay_co_cau_to_chuc')
          },
          accessibility: {
            point: {
              descriptionFormatter: function (point) {
                var nodeName = point.toNode.name,
                  nodeId = point.toNode.id,
                  nodeDesc = nodeName === nodeId ? nodeName : nodeName + ', ' + nodeId,
                  parentDesc = point.fromNode.id;
                return point.index + '. ' + nodeDesc + ', reports to ' + parentDesc + '.';
              }
            }
          },
          series: [
            {
              type: 'organization',
              name: 'Easia',
              keys: ['from', 'to'],
              data: [

                ["PHH Group", "Easia"],
                ["Easia", "Easia-BOM"],
                ["Easia", "Easia-LEGAL & COMPLIANCE"],
                ["Easia", "Easia-ACCOUNTING & FINANCE"],
                ["Easia", "Easia-SALES"],
                ["Easia", "Easia-OPERATION"],
                ["Easia", "Easia-RESERVATION"],
                ["Easia", "Easia-TICKETING & VISA & TRANSPORTATION"],

                // ["Easia", "Easia-IT"],
                // ["Easia", "Easia-MARKETING & BRANDING"],
                // ["Easia", "Easia-CONTRACTING"],
                // ["Easia", "Easia-DATABASE"],
                // ["Easia", "Easia-PROJECT"],
                // ["Easia", "Easia-HUMAN RESOURCES & ADMIN"],
                // ["Easia", "Easia-PRODUCTION RESEARCH & DEVELOPMENT CONSULTANT"],
                // ["Easia", "Easia-PRODUCTION"],
                // ["Easia", "Easia-TRAINING & DEVELOPMENT"],
                // ["Easia", "Easia-ACCOUNTING"],
                // ["PHH Group", "TVE"],
                // ["TVE", "112221121"],
                // ["TVE", "2312312"],
                // ["TVE", "2121212c"],
                // ["2121212c", "XYZ"],
                // ["2121212c", "ABC"],
                // ["PHH Group", "Exotic"],
                // ["Exotic", "Exotic-Sales & Marketing"],
                // ["Exotic", "Exotic-Sales"],
                // ["Exotic", "Exotic-Marketing"],
                // ["Exotic", "Exotic-Operation "],
                // ["Exotic", "Exotic-Accounting"],
                // ["PHH Group", "Begodi"],
                // ["Begodi", "Begodi-Director"],
                // ["Begodi", "Begodi-Russian Market"],
                // ["Begodi", "Begodi-Marketing"],
                // ["Begodi", "Begodi-Sales"],
                // ["Begodi", "Begodi-Contracting"],
                // ["Begodi", "Begodi-Accounting "],
                // ["Begodi", "Begodi-Salas"],
                // ["PHH Group", "AVANA"]

                // ['Shareholders', 'Board'],
                // ['Board', 'CEO'],
                // ['CEO', 'CTO'],
                // ['CEO', 'CPO'],
                // ['CEO', 'CSO'],
                // ['CEO', 'CMO'],
                // ['CEO', 'HR'],
                // ['CTO', 'Product'],
                // ['CTO', 'Web'],
                // ['CSO', 'Sales'],
                // ['CMO', 'Market']

              ],
              levels: [
                {
                  level: 0,
                  color: 'purple',
                  dataLabels: {
                    color: 'white'
                  },
                  height: 25
                },
                {
                  level: 1,
                  color: 'black',
                  dataLabels: {
                    color: 'white'
                  },
                  height: 25
                },
                // {
                //   level: 2,
                //   color: '#9e226d',
                //   dataLabels: {
                //     color: 'white',
                //   },
                // },
                {
                  level: 3,
                  color: '#3aaa59'
                },
                {
                  level: 4,
                  color: '#d46c13'
                }
              ],
              nodes: [

                // {
                //   id: 'Shareholders'
                // },
                // {
                //   id: 'Board'
                // },
                // {
                //   id: 'CEO',
                //   title: 'CEO',
                //   name: 'Grethe Hjetland',
                //   image: 'https://wp-assets.highcharts.com/www-highcharts-com/blog/wp-content/uploads/2018/11/12132317/Grethe.jpg'
                // },
                // {
                //   id: 'HR',
                //   title: 'HR/CFO',
                //   name: 'Anne Jorunn Fjærestad',
                //   color: '#007ad0',
                //   image: 'https://wp-assets.highcharts.com/www-highcharts-com/blog/wp-content/uploads/2018/11/12132314/AnneJorunn.jpg',
                //   column: 3,
                //   offset: '75%'
                // },
                // {
                //   id: 'CTO',
                //   title: 'CTO',
                //   name: 'Christer Vasseng',
                //   column: 4,
                //   image: 'https://wp-assets.highcharts.com/www-highcharts-com/blog/wp-content/uploads/2018/11/12140620/Christer.jpg',
                //   layout: 'hanging'
                // },
                // {
                //   id: 'CPO',
                //   title: 'CPO',
                //   name: 'Torstein Hønsi',
                //   column: 4,
                //   image: 'https://wp-assets.highcharts.com/www-highcharts-com/blog/wp-content/uploads/2018/11/12131849/Torstein1.jpg'
                // },
                // {
                //   id: 'CSO',
                //   title: 'CSO',
                //   name: 'Anita Nesse',
                //   column: 4,
                //   image: 'https://wp-assets.highcharts.com/www-highcharts-com/blog/wp-content/uploads/2018/11/12132313/Anita.jpg',
                //   layout: 'hanging'
                // },
                // {
                //   id: 'CMO',
                //   title: 'CMO',
                //   name: 'Vidar Brekke',
                //   column: 4,
                //   image: 'https://wp-assets.highcharts.com/www-highcharts-com/blog/wp-content/uploads/2018/11/13105551/Vidar.jpg',
                //   layout: 'hanging'
                // },
                // {
                //   id: 'Product',
                //   name: 'Product developers'
                // },
                // {
                //   id: 'Web',
                //   name: 'Web devs, sys admin'
                // },
                // {
                //   id: 'Sales',
                //   name: 'Sales team'
                // },
                // {
                //   id: 'Market',
                //   name: 'Marketing team'
                // }

               /* {id: "Easia", layout: "hanging"},
                {id: "TVE", layout: "hanging"},
                {id: "Exotic", layout: "hanging"},
                {id: "Begodi", layout: "hanging"},
                {id: "2121212c", layout: "hanging"},
                {id: "2121212c", layout: "hanging"},
                {
                  id: "Easia-PRODUCTION RESEARCH & DEVELOPMENT CONSULTANT",
                  dataLabels: {
                    style: {
                      fontSize: "smaller"
                    },
                  },
                },*/
              ],
              colorByPoint: false,
              color: '#007ad0',
              dataLabels: {
                color: 'white'
              },
              borderColor: 'white',
              nodeWidth: 65
            }
          ],
          tooltip: {
            outside: true
          },
          exporting: {
            allowHTML: true,
            sourceWidth: 800,
            sourceHeight: 600
          }
        }
      };
    },
    methods: {
      fetchTree(list, outputs, nodes) {
        for (const [key, item] of Object.entries(list)) {
          if (item.level === 1) {
            outputs.push(['PHH Group', item.name]);
          }
          let hasNode = false;
          let newNode = {
            id: item.name
          };
          // console.log(item.name, item.name.length);
          if (item.name.length > 40) {
            hasNode = true;
            newNode.dataLabels = {
              style: {
                fontSize: "smaller"
              }
            };
          }
          //let newItem = [item.name];
          if (item.children.length > 0) {
            newNode.layout = 'hanging';
            hasNode = true;
            for (const [key, child] of Object.entries(item.children)) {
              let newItemHasChild = [item.name, child.name];
              outputs.push(newItemHasChild);
            }
            this.fetchTree(item.children, outputs, nodes);
          }
          // else {
          //   if (item.level === 1) {
          //     outputs.push(newItem)
          //   }
          // }
          if (hasNode === true) {
            nodes.push(newNode);
          }
        }
        return outputs;
      }
    },
    mounted() {
      let organizations = [];
      let nodes = [];
      //fetch tree data
      this.fetchTree(this.tree, organizations, nodes);
      //Set chart options
      this.chartOptions.series[0].data = organizations;
      this.chartOptions.series[0].nodes = nodes;
      //Set height for chart base on number of organizations
      if (organizations.length < 20) {
        this.chartOptions.chart.height = 1300;
        this.chartOptions.exporting.sourceHeight = 1300;
      } else if (organizations.length > 20 && organizations.length < 35) {
        this.chartOptions.chart.height = 1600;
        this.chartOptions.exporting.sourceHeight = 1600;
        this.chartOptions.exporting.sourceWidth = 1000;
      } else if (organizations.length > 35 && organizations.length < 50) {
        this.chartOptions.chart.height = 1900;
        this.chartOptions.exporting.sourceHeight = 1900;
        this.chartOptions.exporting.sourceWidth = 1000;
      } else if (organizations.length > 50) {
        this.chartOptions.chart.height = 2200;
        this.chartOptions.exporting.sourceHeight = 2200;
        this.chartOptions.exporting.sourceWidth = 1000;
      }
    }
  };
</script>

<style scoped>
  /* Not working => move to style.css */
</style>
