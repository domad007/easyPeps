import React, { Component } from 'react';
import { StyleSheet, View, TouchableOpacity, Text, ScrollView, flexDirection, AsyncStorage, ActivityIndicator } from 'react-native';
import { DataTable } from 'react-native-paper';
class CahierCotes extends Component {
    constructor(props){
        super(props);
        this.state = {
            cotes: []
        }
    }

    componentDidMount(){
        AsyncStorage.multiGet(['idUser', 'idGroup']).then(this.cotes)
        /*AsyncStorage.getItem('idUser').then(this.cotes);
        AsyncStorage.getItem('idGroup').then(this.cotes);*/
    }

    cotes = (value) => {
        fetch('http://192.168.1.3/moyenneEleves/'+value[1][1]+'/'+value[0][1])
        .then((response) => response.json())
        .then((responseJson) => {
            if(responseJson == "probleme"){
                Alert.alert(
                    "",
                    "Vous n'avez pas créé d'élèves, veuillez en créer sur notre site web",
                    [
                        {
                            text: "OK", onPress: () => this.props.navigation.navigate("MenuGroup") 
                        }
                    ]
                );
            }
            else {
                this.setState({cotes: responseJson})
            }
        })
    }

    render(){
        let cotes = this.state.cotes
        let moyennes = [];
        let header= [];
        if(cotes.length === 0) {
            return( 
                <View style={style.loading}>
                    <ActivityIndicator size="large" color="red" />
                </View>
            )
        } 
        let keys = Object.keys(cotes[0]);
        for(let i = 0; i<keys.length; i++){
            header.push(
                <DataTable.Title>{ keys[i] }</DataTable.Title>
            )
        }
        for(let i = 0; i<cotes.length; i++){
            <DataTable.Row>
            {moyennes.push(
                <DataTable.Cell>{cotes[i]['Nom']}</DataTable.Cell>      
            )}
            </DataTable.Row>
        }
        return (
            <ScrollView>
                <DataTable>
                    <DataTable.Header>
                        { header }
                    </DataTable.Header>
                    { moyennes }
                </DataTable>
            </ScrollView>
        )
    }
}

const style= StyleSheet.create({
    loading: {
        flex: 1,
        alignItems: "center",
        justifyContent: "center",
    }
})

export default CahierCotes