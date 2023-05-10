import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { ToggleControl, PanelBody, PanelRow } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Show Table Columns', 'curtis-api' ) }
					initialOpen={ true }
				>
					<PanelRow>
						<ToggleControl
							label="ID"
							onChange={ ( newval ) =>
								setAttributes( { col1: newval } )
							}
							checked={ attributes.col1 }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'First Name', 'curtis-api') }
							onChange={ ( newval ) =>
								setAttributes( { col2: newval } )
							}
							checked={ attributes.col2 }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Last Name', 'curtis-api') }
							onChange={ ( newval ) =>
								setAttributes( { col3: newval } )
							}
							checked={ attributes.col3 }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Email', 'curtis-api') }
							onChange={ ( newval ) =>
								setAttributes( { col4: newval } )
							}
							checked={ attributes.col4 }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Date', 'curtis-api') }
							onChange={ ( newval ) =>
								setAttributes( { col5: newval } )
							}
							checked={ attributes.col5 }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<CurtisAPItable { ...attributes } />
			</div>
		</>
	);
}

export function CurtisAPItable( attr ) {
	const [ error, setError ] = useState( null );
	const [ apitable, setPost ] = useState( null );
	const [ isLoaded, setIsLoaded ] = useState( false );

	/*
	 * useEffect is a React hook that triggers during the component's life
	 * cycle parts, but when giving it an empty array as a second argument
	 * it will only trigger on mounting the component
	 */
	useEffect( () => {
		apiFetch( { path: '/curtis/v1/apicall' } ).then(
			( result ) => {
				setIsLoaded( true );
				setPost( result );
			},
			( error ) => {
				setIsLoaded( true );
				setError( error );
			}
		);
	}, [] ); // empty argument ensure only runs on first render

	if ( error ) {
		return (
			<p className="status">
				<strong>ERROR: { error.message }</strong>
			</p>
		);
	} else if ( ! isLoaded ) {
		return (
			<p className="status">
				<em>{ __( 'Loading table from API...', 'curtis-api') }</em>
			</p>
		);
	} else if ( apitable ) {
		return renderTable( apitable, attr );
	}
	return (
		<p>{ __( 'No Data to Show!', 'curtis-api') }</p>
	);
}

function renderTable( apitable, attr ) {
	return (
		<>
			<h3>{ apitable.title }</h3>
			<div className="table">
				{ renderTableHeader( apitable.data.headers, attr ) }
				{ renderTableRows( apitable.data.rows, attr ) }
			</div>
		</>
	);
}

function renderTableHeader( data, attr ) {
	const header = Object.values( data );
	const output = [];
	let i = 1;
	for ( const [ index, value ] of header.entries() ) {
		if ( attr[ 'col' + i ] ) {
			let col = 'td col' + i;
			output.push(
				<div className={ col } key={ index }>
					{ value }
				</div>
			);
		}
		i++;
	}
	return <div className="tr th">{ output }</div>;
}

function renderTableRows( data, attr ) {
	const rows = Object.values( data );
	const output = [];
	for ( const [ key, value ] of Object.entries( rows ) ) {
		output.push(
			<div className="tr" key={ key }>
				{ renderTableCells( value, attr ) }
			</div>
		);
	}
	return output;
}

function renderTableCells( row, attr ) {
	const output = [];
	let i = 1;
	for ( const [ key, value ] of Object.entries( row ) ) {
		let cell = value;
		if ( key == 'date' ) {
			let date = new Date( row.date * 1000 );
			cell = date.toLocaleDateString();
		}
		if ( attr[ 'col' + i ] ) {
			let col = 'td col' + i;
			output.push(
				<div className={ col } key={ key }>
					{ cell }
				</div>
			);
		}
		i++;
	}
	return output;
}
