import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import RoomItem from './RoomItem';

class Room extends Component {

    displayRoom() {
        const _RoomItem = this.props.rooms.map((value, key) => {
            return 
            <RoomItem 
                key={key} 
                id={value.id}
                space_type_name={value.space_type_name}
                overall_rating={value.overall_rating}
                reviews_count={value.reviews_count}
                host_name={value.host_name}
                status={value.status}
                property_address={value.property_address}
                beds={value.beds}
                bedrooms={value.bedrooms}
                bathrooms={value.bathrooms}
                property_type_name={value.property_type_name}
                property_photo={value.property_photo}>

            { value.name }
            
            </RoomItem>
        });

        return _RoomItem;
    }

    render() {
        return (
            <div className="RoomItem">
                { this.displayRoom() }
            </div>
        )
    }
}
export default Room;