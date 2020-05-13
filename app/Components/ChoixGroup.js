import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, AsyncStorage } from 'react-native';

class ChoixGroup extends Component {
    constructor(props){
        super(props);
        this.state = {
            groups: [],
        }
    }

    componentDidMount(){
        AsyncStorage.getItem('idUser').then(this.getGroups)
    }

    getGroups = (value) => {
        fetch('http://192.168.1.3/groupsUser/'+value)
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                alert("Vous n'avez pas de groupes disponible, veuillez en créer sur notre site web")
            }
            else {
                this.setState({groups: responseJson})
            }
        })
        
    }

    render(){        
        let groups = this.state.groups;
        let ecoles = [];
        for(let i = 0; i<groups.length; i++){
            ecoles.push(
                <View key = { i }>
                    <TouchableOpacity style={style.button} onPress={() => this.props.navigation.navigate('MenuGroup', AsyncStorage.setItem('idGroup', groups[i]['groups_id']))}>
                        <Text style={{ fontSize: 30 }}>{groups[i]['groupes']}</Text>
                        <Text style={{ fontSize: 15 }}>{groups[i]['ecole']}</Text>
                    </TouchableOpacity>
                </View>
            )
        }
        return (
            <ScrollView>
                { ecoles }
            </ScrollView>
        )
    }
}
const style= StyleSheet.create({
    button: {
        flex : 1,
        backgroundColor: '#fff',
        alignItems: 'center',
        justifyContent: 'space-between',
        marginTop: 3,
        marginLeft: 10,
        width: '95%',
        backgroundColor:'lightgrey',
		borderRadius: 25,
		marginVertical: 10,
		paddingVertical: 13,
        textAlign: 'center',
        color: '#FFFFFF'
    }
});
export default ChoixGroup