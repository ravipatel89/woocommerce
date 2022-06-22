/**
 * External dependencies
 */
import { BlockEditorProvider } from '@wordpress/block-editor';
import { ShortcutProvider } from '@wordpress/keyboard-shortcuts';
import { debounce } from 'lodash';
import React, {
	useCallback,
	useEffect,
	useState,
	useRef,
} from '@wordpress/element';

//import '../../styles/gutenberg.css';

/**
 * Internal dependencies
 */
import { EditorWritingFlow } from './editor-writing-flow';
import { registerBlocks } from './registerBlocks';
// import { FixedFormattingToolbar } from './components/FixedFormattingToolbar';
// import { ContextToolbar } from './components/ContextToolbar';
// import { registerFormatTypes } from './formats/register-format-types';
// import { registerCompleters } from './autocomplete';

console.debug( 'registerBlocks', registerBlocks );

//registerBlocks();
// registerFormatTypes();

export const Editor = ( { blocks, onChange, entryId } ) => {
	const blocksRef = useRef( blocks );

	const [ , setRefresh ] = useState( 0 );

	useEffect( () => {
		blocksRef.current = blocks;
		setRefresh( ( r ) => r + 1 );
	}, [ blocks ] );

	return (
		<>
			<BlockEditorProvider
				value={ blocksRef.current }
				settings={ { bodyPlaceholder: '', hasFixedToolbar: true } }
			>
				{ /* <FixedFormattingToolbar /> */ }
				{ /* <ContextToolbar /> */ }

				<ShortcutProvider style={ { height: '100%' } }>
					<EditorWritingFlow />
				</ShortcutProvider>
			</BlockEditorProvider>
		</>
	);
};
